<div x-show="productType === 'variable'"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform -translate-y-4"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     class="space-y-6 border-t border-gray-100 pt-6">

    <div class="flex justify-between items-center">
        <div>
            <h3 class="font-bold text-gray-800 flex items-center">
                <i class="fas fa-layer-group text-emerald-500 mr-2"></i>
                Danh sach bien the hien tai
            </h3>
            <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">Cap nhat gia, kho va hinh anh cho tung loai</p>
        </div>
        <button type="button" @click="addVariant()"
                class="bg-emerald-50 text-emerald-600 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-emerald-500 hover:text-white transition-all duration-200 shadow-sm flex items-center gap-2">
            <i class="fas fa-plus"></i> Them loai moi
        </button>
    </div>

    <div class="space-y-4">
        <template x-for="(variant, index) in variants" :key="index">
            <div class="p-6 border border-gray-100 rounded-2xl bg-gray-50/50 space-y-5 relative group hover:border-emerald-200 transition-colors">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest bg-emerald-50 px-2 py-0.5 rounded">
                        Bien the #<span x-text="index + 1"></span>:
                    </span>
                    <span class="font-bold text-gray-700 text-sm"
                          x-text="Object.values(variant.attributes).filter(v => v !== '').join(' - ') || 'Chua chon thuoc tinh'">
                    </span>
                </div>

                <button type="button" @click="removeVariant(index)"
                        class="absolute top-4 right-4 text-gray-300 hover:text-red-500 transition-colors p-2">
                    <i class="fas fa-trash-alt"></i>
                </button>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Ma SKU</label>
                        <input type="text"
                               :name="`variants[${index}][sku]`"
                               x-model="variant.sku"
                               placeholder="Tu dong"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none bg-white transition">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Gia ban <span class="text-red-500">*</span></label>
                        <input type="number" :name="`variants[${index}][price]`" x-model="variant.price" required min="0"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none bg-white transition font-bold text-emerald-600">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Gia giam</label>
                        <input type="number" :name="`variants[${index}][sale_price]`" x-model="variant.sale_price" min="0"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none bg-white transition font-bold text-emerald-600">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Ton kho <span class="text-red-500">*</span></label>
                        <input type="number" :name="`variants[${index}][stock]`" x-model="variant.stock" required min="0"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none bg-white transition font-semibold">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Anh rieng</label>
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 shrink-0 rounded-lg border border-gray-200 bg-white overflow-hidden flex items-center justify-center">
                                <template x-if="variant.image_url">
                                    <img :src="variant.image_url" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!variant.image_url">
                                    <i class="fas fa-image text-gray-200"></i>
                                </template>
                            </div>

                            <label class="cursor-pointer bg-white border border-gray-200 rounded-xl px-3 py-2 hover:bg-emerald-50 hover:border-emerald-300 flex-1 flex items-center justify-center transition group">
                                <span class="text-[11px] text-gray-500 font-bold group-hover:text-emerald-600">Doi anh</span>
                                <input type="file" :name="`variants[${index}][image]`" accept="image/*" class="sr-only"
                                       @change="
                                            const file = $event.target.files[0];
                                            if (file) {
                                                variant.image_url = URL.createObjectURL(file);
                                            }
                                       ">
                            </label>
                        </div>
                        <input type="hidden" :name="`variants[${index}][existing_image]`" :value="variant.existing_image || ''">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 border-t border-gray-100 pt-5">
                    @foreach($attributes as $attr)
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                            {{ $attr->name }}
                        </label>
                        <select
                            :name="`variants[${index}][attributes][{{ $attr->name }}]`"
                            x-model="variant.attributes['{{ $attr->name }}']"
                            class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition"
                        >
                            <option value="">-- Khong dung --</option>
                            @foreach($attr->attributeValues as $val)
                                <option value="{{ $val->value }}">{{ $val->value }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endforeach
                </div>
            </div>
        </template>

        <div x-show="variants.length === 0"
             class="text-center py-12 border-2 border-dashed border-gray-100 rounded-3xl text-gray-400 bg-gray-50/20">
            <div class="flex flex-col items-center">
                <i class="fas fa-box-open text-2xl opacity-20 mb-3"></i>
                <p class="text-sm font-bold text-gray-500">Chua thiet lap bien the cho san pham nay</p>
                <button type="button" @click="addVariant()" class="mt-3 text-emerald-600 text-xs font-bold underline hover:text-emerald-700">
                    Bam vao day de them
                </button>
            </div>
        </div>
    </div>
</div>
