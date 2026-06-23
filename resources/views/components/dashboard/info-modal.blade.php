<div id="globalGuideModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-950/40 backdrop-blur-sm" onclick="toggleGlobalGuideModal(false)">
    
    <div id="globalModalContent" class="bg-white dark:bg-slate-900 w-full max-w-2xl rounded-[2rem] p-6 md:p-8 shadow-2xl border border-indigo-500/20 max-h-[85vh] overflow-y-auto custom-scroll modal-spec-card" onclick="event.stopPropagation()">
        
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
            <h3 id="globalModalTitle" class="text-lg font-black tracking-tight text-slate-900 dark:text-white uppercase flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-indigo-600"></i> Panduan & Pengertian Metrik Evaluasi
            </h3>
            <button type="button" onclick="toggleGlobalGuideModal(false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <div id="globalModalBody" class="space-y-6 text-sm text-slate-600 dark:text-slate-400 leading-relaxed text-justify pr-1">
            <div>
                <h4 class="font-bold text-slate-900 dark:text-slate-200 mb-1">1. Matriks Konfusi (Confusion Matrix)</h4>
                <p class="text-justify">Grafik yang memetakan performa tebakan model secara detail untuk setiap karakter bahasa isyarat BISINDO. Sumbu X merepresentasikan kelas label yang diprediksi oleh model (Predicted), sedangkan Sumbu Y merepresentasikan kelas label asli data yang sebenarnya (Actual). Garis diagonal utama menunjukkan tebakan Benar. Semakin pekat warnanya, semakin tinggi akurasi sistem.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 dark:text-slate-200 mb-1">2. Distribusi Dataset</h4>
                <p class="text-justify">Grafik batang memperlihatkan volume sebaran data yang digunakan untuk melatih tiap kelas label BISINDO. Tinggi diagram batang yang rata mencerminkan balanced dataset, memastikan model belajar secara adil tanpa condong pada label tertentu.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 dark:text-slate-200 mb-1">3. Akurasi (Accuracy)</h4>
                <p class="text-justify">Mengukur seberapa sering model menebak dengan benar secara keseluruhan dari total seluruh data uji yang diberikan.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 dark:text-slate-200 mb-1">4. Presisi (Precision)</h4>
                <p class="text-justify">Mengukur tingkat ketepatan model. Dari seluruh data yang ditebak sebagai label tertentu (misalnya huruf 'A'), seberapa banyak yang tebakannya benar-benar sesuai dengan kenyataan aslinya.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 dark:text-slate-200 mb-1">5. Recall (Sensitivitas)</h4>
                <p class="text-justify">Mengukur kemampuan model dalam menemukan kembali informasi. Dari seluruh data asli yang seharusnya berlabel tertentu, seberapa banyak sampel yang berhasil dideteksi dengan benar oleh model.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 dark:text-slate-200 mb-1">6. F1-Score</h4>
                <p class="text-justify">Nilai rata-rata harmonis yang menyeimbangkan antara Presisi dan Recall. Metrik ini sangat berguna sebagai indikator performa nyata jika sebaran data pada tiap label mengalami ketimpangan atau tidak rata.</p>
            </div>
        </div>
    </div>
</div>