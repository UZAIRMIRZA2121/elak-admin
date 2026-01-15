    <!-- <div class=" h-100">
        <div class=" d-flex flex-wrap align-items-center">
            <div class="w-100 d-flex flex-wrap __gap-15px">
                <div class="flex-grow-1 mx-auto">
                    <label class="text-dark d-block mb-4 mb-xl-5">
                        {{ translate('messages.item_image') }}
                        <small class="">( {{ translate('messages.ratio') }} 1:1 )</small>
                    </label>
                    <div class="d-flex flex-wrap __gap-12px __new-coba" id="coba"></div>
                </div>
                <div class="flex-grow-1 mx-auto">
                    <label class="text-dark d-block mb-4 mb-xl-5">
                        {{ translate('messages.item_thumbnail') }}
                        @if(Config::get('module.current_module_type') == 'food')
                        <small class="">( {{ translate('messages.ratio') }} 1:1 )</small>
                        @else
                        <small class="text-danger">* ( {{ translate('messages.ratio') }} 1:1 )</small>
                        @endif
                    </label>
                    <label class="d-inline-block m-0 position-relative">
                        @if($product->image ?? false)
                            <img class="img--176 border" id="viewer" src="{{ asset('storage/product/' . $product->image) }}" alt="thumbnail" />
                        @else
                            <img class="img--176 border" id="viewer" src="{{ asset('public/assets/admin/img/upload-img.png') }}" alt="thumbnail" />
                        @endif
                        <div class="icon-file-group">
                            <div class="icon-file"><input type="file" name="image" id="customFileEg1" class="custom-file-input d-none" accept=".webp, .jpg, .png, .webp, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <i class="tio-edit"></i>
                            </div>
                        </div>
                    </label>
                    
                    {{-- Display existing multiple images --}}
                    @php
                        $existingImages = json_decode($product->images ?? '[]', true);
                    @endphp
                    @if(!empty($existingImages))
                        <div class="mt-3">
                            <label class="text-dark d-block mb-2">Existing Images</label>
                            <div class="d-flex flex-wrap gap-2" id="existingImagesContainer">
                                @foreach($existingImages as $index => $image)
                                    @php
                                        $imgPath = is_array($image) ? $image['img'] : $image;
                                    @endphp
                                    <div class="position-relative existing-image-item" data-image="{{ $imgPath }}">
                                        <img src="{{ asset('storage/product/' . $imgPath) }}" 
                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #ddd;">
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-existing-image"
                                                style="padding: 2px 6px; font-size: 10px; border-radius: 50%;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <input type="hidden" name="removedImageKeys" id="removedImageKeys" value="">
                    @endif
                </div>
            </div>
        </div>
    </div>

<script>
$(document).ready(function() {
    // Handle removing existing images
    $(document).on('click', '.remove-existing-image', function(e) {
        e.preventDefault();
        let imageName = $(this).closest('.existing-image-item').data('image');
        let currentRemoved = $('#removedImageKeys').val();
        $('#removedImageKeys').val(currentRemoved ? currentRemoved + ',' + imageName : imageName);
        $(this).closest('.existing-image-item').fadeOut(300, function() {
            $(this).remove();
        });
    });
});
</script> -->




   <div class="col-md-6">
    <div class="card h-100">
        <div class="card-body d-flex flex-wrap align-items-center">
            <div class="w-100 d-flex flex-wrap __gap-15px">
                <div class="flex-grow-1 mx-auto">
                    <label class="text-dark d-block">
                        {{ translate('messages.item_image') }}
                        <small >( {{ translate('messages.ratio') }} 1:1 )</small>
                    </label>
                    <div class="d-flex flex-wrap __gap-12px __new-coba" id="coba"></div>

                    <input type="hidden" id="removedImageKeysInput" name="removedImageKeys" value="">
                    <div class="d-flex flex-wrap __gap-12px mt-3">
                        @php
                            $images = $product->images; 
                            if(is_string($images)){
                                $images = json_decode($images, true);
                            }
                             if(!is_array($images)){
                                $images = [];
                            }
                        @endphp
                        @foreach($images as $key => $photo)
                            @php($photo = is_array($photo)?$photo:['img'=>$photo,'storage'=>'public'])
                            <div id="product_images_{{ $key }}" class="spartan_item_wrapper min-w-176px max-w-176px">
                                <img class="img--square onerror-image"
                                src="{{ \App\CentralLogics\Helpers::get_full_url('product',$photo['img'] ?? '',$photo['storage']) }}"
                                    data-onerror-image="{{ asset('public/assets/admin/img/upload-img.png') }}"
                                    alt="Product image">
                                    {{-- <div class="pen spartan_remove_row"><i class="tio-edit"></i></div> --}}
                                    <a href="#" data-key={{ $key }} data-photo="{{ $photo['img'] }}"
                                    class="spartan_remove_row function_remove_img"><i class="tio-add-to-trash"></i></a>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex-grow-1 mx-auto">
                    <label class="text-dark d-block">
                        {{ translate('messages.item_thumbnail') }}
                        <small class="text-danger">* ( {{ translate('messages.ratio') }} 1:1 )</small>
                    </label>
                    <label class="d-inline-block m-0 position-relative">
                        <img class="img--176 border onerror-image" id="viewer"
                        src="{{ $product['image_full_url'] ?? asset('public/assets/admin/img/upload-img.png') }}"
                            data-onerror-image="{{ asset('public/assets/admin/img/upload-img.png') }}"
                            alt="thumbnail" />
                        <div class="icon-file-group">
                            <div class="icon-file">
                                <input type="file" name="image" id="customFileEg1" class="custom-file-input read-url"
                                    accept=".webp, .jpg, .png, .jpeg, .webp, .gif, .bmp, .tif, .tiff|image/*" >
                                    <i class="tio-edit"></i>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>