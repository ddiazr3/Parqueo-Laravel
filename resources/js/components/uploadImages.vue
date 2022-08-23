<template>
    <div>
        <vue-dropzone
            ref="myVueDropzone"
            id="dropzone"
            v-on:vdropzone-success="success"
            :options="multiple ? dropzoneOptions : dropzoneOptionsOne">
        </vue-dropzone>
    </div>
</template>

<script>
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'

export default {
    props: ['model','multiple'],
    data() {
        return {
            dropzoneOptions: {
                url: '/uploadimages',
                maxThumbnailFilesize: 512,
                maxFiles: 10,
                dictDefaultMessage: "<i class='fa fa-cloud-upload'></i> ARRASTRAR IMÁGENES PARA AGREGAR",
                headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content }
            },
            dropzoneOptionsOne: {
                url: "/uploadimages",
                thumbnailWidth: 150,
                maxFilesize: 0.5,
                maxFiles: 1,
                resizeWidth: 640,
                resizeHeight: 600,
                dictMaxFilesExceeded: "Solo se permite una imagen.",
                dictInvalidFileType:
                    "El archivo no es compatible solo se acepta (jpg,jpeg,png)",
                acceptedFiles: ".jpg, .jpeg, .png",
                dictDefaultMessage:
                    "<i class='fa fa-cloud-upload'></i> ARRASTRAR UNA IMAGEN PARA AGREGAR",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                        .content,
                },
            },
        }
    },
    methods: {
        success(file, response) {
            let foto = {
                foto: response,
                id: 99999
            }
            console.log(foto)
            if(this.multiple){
                this.model.archivo.push(foto)
            }else{
                this.model.foto = foto
            }

        },
    },
    components: {
        vueDropzone: vue2Dropzone
    },
}
</script>
