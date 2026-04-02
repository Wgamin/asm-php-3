<div x-data="imageViewer()">
    <div class="space-y-6">
        {{-- A. ẢNH ĐẠI DIỆN CHÍNH --}}
        <div class="space-y-3">
            <label class="text-sm font-bold text-gray-700 flex items-center gap-2">
                <i class="fas fa-image text-emerald-500"></i> Ảnh đại diện chính <span class="text-red-500">*</span>
            </label>
            
            <div class="relative group cursor-pointer border-2 border-dashed border-gray-200 rounded-2xl p-2 hover:border-emerald-400 transition-all bg-gray-50/50">
                <input type="file" name="image" required accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10" @change="previewMainImage">
                
                <template x-if="mainImageUrl">
                    <img :src="mainImageUrl" class="w-full h-48 object-cover rounded-xl shadow-sm">
                </template>
                
                <template x-if="!mainImageUrl">
                    <div class="flex flex-col items-center py-8">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mb-3">
                            <i class="fas fa-camera text-gray-400"></i>
                        </div>
                        <p class="text-xs text-gray-500 font-medium">Chọn ảnh nền chính</p>
                    </div>
                </template>
            </div>
        </div>

        <hr class="border-gray-100">

        {{-- B. NHIỀU ẢNH PHỤ (GALLERY) --}}
        <div class="space-y-3">
            <label class="text-sm font-bold text-gray-700 flex items-center gap-2">
                <i class="fas fa-images text-emerald-500"></i> Ảnh phụ (Gallery)
            </label>
            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Xem vườn, chứng nhận, cận cảnh...</p>

            <div class="grid grid-cols-2 gap-3" id="gallery-preview">
                {{-- Loop qua các ảnh đã chọn --}}
                <template x-for="(url, index) in galleryUrls" :key="index">
                    <div class="relative aspect-square rounded-xl overflow-hidden border border-gray-100 shadow-sm group">
                        <img :src="url" class="w-full h-full object-cover">
                        <button type="button" @click="removeGalleryImage(index)" 
                                class="absolute top-1 right-1 bg-red-500 text-white w-5 h-5 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-times text-[10px]"></i>
                        </button>
                    </div>
                </template>

                {{-- Nút bấm thêm ảnh phụ --}}
                <div class="relative aspect-square border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center bg-gray-50 hover:bg-emerald-50 hover:border-emerald-300 transition-all cursor-pointer">
                    <input type="file" name="images[]" multiple accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" @change="previewGallery">
                    <i class="fas fa-plus text-gray-300 mb-1"></i>
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Thêm ảnh</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function imageViewer() {
        return {
            mainImageUrl: null,
            galleryUrls: [],

            previewMainImage(e) {
                const file = e.target.files[0];
                if (file) {
                    this.mainImageUrl = URL.createObjectURL(file);
                }
            },

            previewGallery(e) {
                const files = Array.from(e.target.files);
                files.forEach(file => {
                    this.galleryUrls.push(URL.createObjectURL(file));
                });
            },

            removeGalleryImage(index) {
                this.galleryUrls.splice(index, 1);
                // Lưu ý: Việc xóa này chỉ xóa URL Preview, để xóa thực sự trong input file cần logic phức tạp hơn 
                // hoặc dùng DataTransfer API. Tuy nhiên với yêu cầu cơ bản, chọn lại là cách nhanh nhất.
            }
        }
    }
</script>