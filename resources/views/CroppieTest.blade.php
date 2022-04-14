@extends('backend.master')
@section('title', '商品主檔 - 新增基本資訊')
@section('content')
    <style>
        .ondragover {
            background: #b7e0fb !important;
            transition: background-color 0.5s;
            /* background: #ce1f59 !important; */
        }

        .elements-box>tr>td>* {
            pointer-events: none;
        }

        .img-box {
            height: 160px;
            /*can be anything*/
            width: 160px;
            /*can be anything*/
            position: relative;
            background-color: rgb(90, 86, 86);
            border: 1px solid black;
        }

        .img-box img {
            max-height: 100%;
            max-width: 100%;
            width: auto;
            height: auto;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
        }

    </style>
    <h1>測試畫面</h1>
    <br>
    <div id="ImageUploadBox">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">

                    <div class="col-sm-1 ">
                        <label class="control-label">商品圖檔</label>
                    </div>
                    <div class="col-sm-10">
                        <p class="help-block">最多上傳15張，每張size不可超過1MB，副檔名須為JPG、JPEG、PNG</p>
                        <input type="file" @change="fileSelected" multiple accept=".jpg,.jpeg,.png">
                        <input style="display: none" type="file" :ref="'images_files'" name="filedata[]" multiple>
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-sm-2 col-md-2" v-for="(image, key) in images" :key="key">
                <div class="thumbnail" @dragstart="drag" @dragover='dragover' @dragleave='dragleave' @drop="drop"
                    :data-index="key" :data-type="'image'" draggable="true" style="pointer-events: auto;">
                    <div class="img-box" style="pointer-events: none;">
                        <img :ref="'image'">
                    </div>
                    <div class="caption" style="pointer-events: none;">
                        <p>檔案名稱: @{{ image.name }}</p>
                        <p>檔案大小:@{{ image.sizeConvert }}</p>
                        <p>
                            排序: @{{ key + 1 }}
                            <button class="btn btn-danger pull-right btn-events-none" type="button" @click="delImages(key)"
                                style="pointer-events: auto;">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        var ImageUpload = Vue.extend({
            data: function() {
                return {
                    images: [],
                }
            },
            methods: {
                fileSelected(e) {
                    let vm = this;
                    var selectedFiles = e.target.files;
                    if (selectedFiles.length + this.images.length > 15) {
                        alert('不能超過15張照片');
                        e.target.value = '';
                        return false;
                    }
                    for (let i = 0; i < selectedFiles.length; i++) {
                        let type = selectedFiles[i].type;

                        if (selectedFiles[i].size > 1048576) {
                            alert('照片名稱:' + selectedFiles[i].name + '已經超出大小');
                        } else if (type !== 'image/jpeg' && type !== 'image/png') {
                            alert('照片名稱:' + selectedFiles[i].name + '格式錯誤');
                        } else {
                            this.images.push(selectedFiles[i]);
                        }
                    }
                    this.adjustTheDisplay();
                    this.images.map(function(value, key) {
                        value.sizeConvert = vm.formatBytes(value.size);
                    });
                    e.target.value = '';
                },
                delImages(index) {
                    this.$delete(this.images, index);
                    this.adjustTheDisplay();
                },
                imagesCheck() {
                    console.log('-----------------------');
                    console.log(this.images);
                },
                formatBytes(bytes, decimals = 2) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const dm = decimals < 0 ? 0 : decimals;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
                },
                drag(eve) {
                    eve.dataTransfer.setData("text/index", eve.target.dataset.index);
                    eve.dataTransfer.setData("text/type", eve.target.dataset.type);
                    $('.btn-events-none').css('pointer-events', 'none');
                },
                dragover(eve) {
                    eve.preventDefault();
                    eve.target.parentNode.classList.add('ondragover');
                    $('.btn-events-none').css('pointer-events', 'auto');

                },
                dragleave(eve) {
                    eve.target.parentNode.classList.remove('ondragover');
                    $('.btn-events-none').css('pointer-events', 'auto');
                    eve.preventDefault();
                },
                drop(eve) {
                    let vm = this;
                    $('.btn-events-none').css('pointer-events', 'auto');
                    eve.target.parentNode.classList.remove('ondragover');
                    var index = eve.dataTransfer.getData("text/index");
                    var type = eve.dataTransfer.getData("text/type");
                    let targetIndex = eve.target.dataset.index;
                    let targetType = eve.target.dataset.type;
                    var item = this.images[index];
                    this.images.splice(index, 1);
                    this.images.splice(targetIndex, 0, item);
                    this.adjustTheDisplay();
                },
                adjustTheDisplay() {
                    let list = new DataTransfer();
                    for (let i = 0; i < this.images.length; i++) {
                        list.items.add(this.images[i]);
                        let reader = new FileReader();
                        reader.onload = (e) => {
                            this.$refs.image[i].src = reader.result;
                        };
                        reader.readAsDataURL(this.images[i]);
                    }
                    this.$refs.images_files.files = list.files;

                },
            },
            computed: {},
            watch: {},
        })
        new ImageUpload().$mount('#ImageUploadBox');
        var el = document.getElementById('vanilla-demo');
        var vanilla = new Croppie(el, {
            viewport: {
                width: 100,

                height: 100
            },
            boundary: {
                width: 600,
                height: 600
            },
            showZoomer: false,
            enableOrientation: true
        });
        vanilla.bind({
            url: 'http://pc.laravel.uec/asset/img/bg-01.jpg',
            orientation: 4
        });
        //on button click
        vanilla.result('blob').then(function(blob) {
            // do something with cropped blob
        });
    </script>
@endsection
