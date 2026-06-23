const videoElement = document.getElementById('input_video');
const canvasElement = document.getElementById('output_canvas');
const canvasCtx = canvasElement.getContext('2d');
const statusText = document.getElementById('status');

let lastPredictTime = 0;
let onnxSession = null;
let classLabels = [];
let isModeChangingNotification = false;

window.currentDetectionMode = 'huruf'; 

async function initModelAndLabels() {
    try {
        statusText.style.display = 'block';
        statusText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat Model AI ke Browser...';
        
        // 1. Fetch metadata label encoder hasil training python
        const metaResponse = await fetch('/models/meta_model.json');
        const metaData = await metaResponse.json();
        classLabels = metaData.classification_report ? Object.keys(metaData.classification_report).filter(k => k !== 'accuracy' && k !== 'macro avg' && k !== 'weighted avg') : [];
        
        // 2. Load engine ONNX runtime web session
        onnxSession = await ort.InferenceSession.create('/models/rf_model.onnx');      
        console.log("✅ ONNX Session & Class Labels Berhasil Dimuat!");

        statusText.innerHTML = '<i class="fas fa-check-circle" style="color: #10b981;"></i> AI Siap! Posisikan tangan Anda';
    } catch (error) {
        console.error("⚠️ Gagal memuat aset model local:", error);
        statusText.innerHTML = '<i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i> Gagal Memuat Model AI';
    }
}

// FUNGSI KHUSUS UPDATE WARNA TOMBOL
window.updateButtonVisuals = function(mode) {
    const btnHuruf = document.getElementById('btnModeHuruf');
    const btnAngka = document.getElementById('btnModeAngka');
    
    if (btnHuruf && btnAngka) {
        const style = getComputedStyle(document.documentElement);
        const textMutedColor = style.getPropertyValue('--text-muted').trim() || '#71717a';
        const blueColor = style.getPropertyValue('--blue').trim() || '#2563eb';
        const greenColor = style.getPropertyValue('--green').trim() || '#10b981';

        if (mode === 'huruf') {
            btnHuruf.style.background = blueColor;
            btnHuruf.style.color = '#fff';
            btnHuruf.style.boxShadow = '0 2px 8px rgba(37, 99, 235, 0.35)';
            
            btnAngka.style.background = 'transparent';
            btnAngka.style.color = textMutedColor;
            btnAngka.style.boxShadow = 'none';
        } else if (mode === 'angka') {
            btnAngka.style.background = greenColor;
            btnAngka.style.color = '#fff';
            btnAngka.style.boxShadow = '0 2px 8px rgba(16, 185, 129, 0.35)';
            
            btnHuruf.style.background = 'transparent';
            btnHuruf.style.color = textMutedColor;
            btnHuruf.style.boxShadow = 'none';
        }
    }
}

// PENGENDALI UTAMA PERGANTIAN MODE DENGAN PENGUNCI 1 DETIK & ANIMASI RINGAN
window.changeMode = function(mode) {
    window.currentDetectionMode = mode;
    
    // Update tampilan tombol secara visual
    window.updateButtonVisuals(mode);

    // AKTIFKAN LOCK SISTEM NOTIFIKASI TRANSISI STATUS TEKS
    isModeChangingNotification = true;
    statusText.style.display = 'block';
    
    // Mengganti fa-pulse yang berat dengan kombinasi ikon statis yang clean dan ringan
    if (mode === 'huruf') {
        statusText.innerHTML = '<i class="fas fa-font" style="color: #3b82f6;"></i> Mengalihkan ke <b>Mode HURUF (A - Z)</b>';
    } else if (mode === 'angka') {
        statusText.innerHTML = '<i class="fas fa-hashtag" style="color: #10b981;"></i> Mengalihkan ke <b>Mode ANGKA (0 - 9)</b>';
    }

    // Dipangkas tepat menjadi 1 detik (1000ms) agar terasa instan dan snappy
    setTimeout(() => {
        isModeChangingNotification = false;
    }, 1000);

    console.log("🔄 Mode deteksi aktif berganti ke:", window.currentDetectionMode);
}

function extractFeatures(results) {
    let features = new Array(126).fill(0);
    if (results.multiHandLandmarks && results.multiHandLandmarks.length > 0) {
        let handList = results.multiHandLandmarks.map((lm, i) => ({
            lm, label: results.multiHandedness[i].label
        })).sort((a, b) => a.label.localeCompare(b.label));

        const base_x = handList[0].lm[0].x;
        const base_y = handList[0].lm[0].y;
        const base_z = handList[0].lm[0].z;

        handList.forEach((hand, index) => {
            if (index < 2) {
                let offset = index * 63;
                hand.lm.forEach((lm, lmIdx) => {
                    let i = offset + (lmIdx * 3);
                    features[i]     = lm.x - base_x;
                    features[i + 1] = lm.y - base_y;
                    features[i + 2] = lm.z - base_z;
                });
            }
        });
    }
    return features;
}

function updateUI(prediksi, akurasi) {
    document.getElementById('prediction-result').innerText = prediksi;
    document.getElementById('accuracy-value').innerText = akurasi + "%";
    document.getElementById('accuracy-bar').style.width = akurasi + "%";
}

function drawGuide(ctx, width, height) {
    ctx.strokeStyle = "rgba(59,130,246,0.45)";
    ctx.lineWidth = 3;
    ctx.setLineDash([12, 8]);
    const rectW = width * 0.58;
    const rectH = height * 0.70;
    const rectX = (width - rectW) / 2;
    const rectY = (height - rectH) / 2;

    ctx.strokeRect(rectX, rectY, rectW, rectH);
    ctx.setLineDash([]);
    ctx.fillStyle = "rgba(255,255,255,0.9)";
    ctx.font = "bold 14px Inter";
    ctx.textAlign = "center";
    ctx.fillText("POSISIKAN TANGAN DI SINI", width / 2, rectY - 14);
}

async function runLocalPrediction(features) {
    if (!onnxSession) return;

    try {
        const inputTensor = new ort.Tensor('float32', new Float32Array(features), [1, 126]);
        const outputNames = onnxSession.outputNames;
        const labelOutputName = outputNames[0]; 
        const probOutputName = outputNames[1];  

        const feeds = { 'float_input': inputTensor };
        const outputMap = await onnxSession.run(feeds, outputNames);
        const labelTensor = outputMap[labelOutputName];
        
        if (labelTensor && labelTensor.data) {
            const predictedIndex = Number(labelTensor.data[0]);
            // Menggunakan 'let' agar nilai stringLabel bisa dimanipulasi oleh logika pemetaan
            let stringLabel = classLabels[predictedIndex] || "-";

            // =========================================================================
            // LOGIKA PEMETAAN AMBIGUITAS GESTUR KEMBAR (HURUF <-> ANGKA)
            // =========================================================================
            if (window.currentDetectionMode === 'angka') {
                const upperLabel = stringLabel.toUpperCase();
                
                if (upperLabel === 'V') {
                    stringLabel = '2';
                } 
                // Mengatasi bottleneck angka 6 s/d 9 yang sering terbaca sebagai W atau F
                else if (upperLabel === 'W') {
                    stringLabel = '6'; // Mapping default atau disesuaikan dengan intensitas kemiripan tertinggi
                } 
                else if (upperLabel === 'F') {
                    stringLabel = '9';
                } 
                else if (upperLabel === 'B') {
                    stringLabel = '4';
                }
            } else if (window.currentDetectionMode === 'huruf') {
                // Kebalikannya jika berada di mode huruf namun model mendeteksi angka bawaannya
                if (stringLabel === '2') stringLabel = 'V';
                else if (stringLabel === '6' || stringLabel === '7' || stringLabel === '8') stringLabel = 'W';
                else if (stringLabel === '9') stringLabel = 'F';
                else if (stringLabel === '4') stringLabel = 'B';
            }

            // =========================================================================
            // VALIDASI DAN FILTER REGEX BERDASARKAN MODE AKTIF
            // =========================================================================
            let isLabelAllowed = true;
            const isAngka = /^[0-9]$/.test(stringLabel);

            if (window.currentDetectionMode === 'angka' && !isAngka) {
                isLabelAllowed = false; 
            } else if (window.currentDetectionMode === 'huruf' && isAngka) {
                isLabelAllowed = false; 
            }

            // =========================================================================
            // PROSES SELEKSI SKOR AKURASI (CONFIDENCE SCORE)
            // =========================================================================
            let confidenceScore = "0"; 
            if (probOutputName && outputMap[probOutputName]) {
                const probTensor = outputMap[probOutputName];
                if (probTensor.data) {
                    const maxRawScore = Math.max(...probTensor.data);
                    confidenceScore = (maxRawScore * 100).toFixed(1); 
                }
            }

            // =========================================================================
            // EVALUASI KELAYAKAN UI & RENDER NOTIFIKASI STATUS
            // =========================================================================
            if (isLabelAllowed && classLabels.length > 0 && predictedIndex < classLabels.length) {
                if (parseFloat(confidenceScore) > 30.0) {
                    updateUI(stringLabel, confidenceScore); 

                    if (!isModeChangingNotification) {
                        statusText.innerHTML = `<i class="fas fa-hand-sparkles" style="color: #10b981;"></i> Tangan terdeteksi.`;
                    }
                } else {
                    updateUI("-", "0"); 
                    if (!isModeChangingNotification) {
                        statusText.innerHTML = '<i class="fas fa-hand-paper" style="color: #f59e0b;"></i> Posisi gestur kurang jelas...';
                    }
                }
            } else {
                updateUI("-", "0");
                if (!isModeChangingNotification) {
                    statusText.innerHTML = `<i class="fas fa-ban" style="color: #ef4444;"></i> Isyarat diabaikan (Bukan Mode ${window.currentDetectionMode.toUpperCase()})`;
                }
            }
        }
    } catch (err) {
        console.error("Gagal melakukan prediksi ONNX lokal untuk Pengenalan Isyarat:", err);
    }
}

function onResults(results) {
    canvasElement.width = videoElement.videoWidth;
    canvasElement.height = videoElement.videoHeight;
    canvasCtx.save();
    canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);
    
    canvasCtx.save();
    canvasCtx.translate(canvasElement.width, 0);
    canvasCtx.scale(-1, 1);
    canvasCtx.drawImage(results.image, 0, 0, canvasElement.width, canvasElement.height);

    if (results.multiHandLandmarks) {
        results.multiHandLandmarks.forEach((landmarks, index) => {
            const handedness = results.multiHandedness[index].label;
            const handColor = handedness === 'Right' ? '#6366f1' : '#10b981';

            drawConnectors(canvasCtx, landmarks, HAND_CONNECTIONS, {
                color: handColor,
                lineWidth: 4
            });
            drawLandmarks(canvasCtx, landmarks, {
                color: '#ffffff',
                lineWidth: 1.5,
                radius: 3
            });
        });
    }
    canvasCtx.restore();
    drawGuide(canvasCtx, canvasElement.width, canvasElement.height);
    canvasCtx.restore();

    if (results.multiHandLandmarks && results.multiHandLandmarks.length > 0) {
        const now = Date.now();
        if (now - lastPredictTime > 200) {
            lastPredictTime = now;
            const features = extractFeatures(results);
            runLocalPrediction(features);
        }
    } else {
        updateUI("-", 0);
        
        // Jeda waktu ketat mencegah teks pencarian menimpa notifikasi pergantian mode
        if (!isModeChangingNotification) {
            statusText.innerHTML = '<i class="fas fa-video" style="color: #9ca3af;"></i> Mencari Tangan di Area Kamera...';
        }
    }
}

const hands = new Hands({
    locateFile: (file) => {
        return `https://cdn.jsdelivr.net/npm/@mediapipe/hands/${file}`;
    }
});

hands.setOptions({
    maxNumHands: 2,
    modelComplexity: 1,
    minDetectionConfidence: 0.6,
    minTrackingConfidence: 0.6
});

hands.onResults(onResults);

const camera = new Camera(videoElement, {
    onFrame: async () => {
        await hands.send({
            image: videoElement
        });
    },
    width: 1280,
    height: 720
});

window.addEventListener('DOMContentLoaded', async () => {
    await initModelAndLabels();
    camera.start();
    window.updateButtonVisuals(window.currentDetectionMode);
});