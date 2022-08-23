<template>
    <div>
        <div class="col-sm-3">
            <vue-simple-upload
                :options="options"
                :multiple="options.multiple=multiple"
                :url="options.url='/inspecciones/'+id+'/upload'"
                @progress-update="progressUpdate"
                @file-size-error="fileSizeError"
                ref="fileUploadComp"
            >
            </vue-simple-upload>
        </div>
        <div class="clearfix" v-if="options.multiple"></div>
        <div class="col-sm-9">
            <div class="table-responsive">
                <table :class="'table table-condensed'" v-show="fileInfoList.length > 0">
                    <tr v-if="options.multiple" class="section-title">
                        <td>Archivo</td>
                        <td>% Completado</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr v-for="(fileInfo, index) in fileInfoList" :key="index">
                        <td v-if="options.multiple">
                            {{ fileInfo.fileName }}
                        </td>
                        <td v-if="fileInfo.response==='Error tipo de archivo'">
                            <span class="label label-danger"><i class="fa fa-minus-circle"></i>Error: Archivo inválido. Utilice png, jpg, jpeg.</span>
                        </td>
                        <td>
                            <div v-if="fileInfo.response!=='Error tipo de archivo'&&fileInfo.type!=='success'" class="progress progress-xs progress-striped active">
                                <div v-if="fileInfo.type==='uploading'" class="progress-bar" :class="'progress-bar-success'" :style="{ width: fileInfo.type === 'uploading' ? fileInfo.progress : '100%' }"></div>
                            </div>
                            <i v-if="fileInfo.type==='success'">100% Completado</i>
                            <i v-if="fileInfo.type==='uploading'">{{ parseInt(fileInfo.fileInfo.size / 1024, 10) }}kb<img src="/images/loader.gif"></i>
                        </td>
                        <td>
                            <i class="fa fa-close fl-l" v-if="fileInfo.type === 'success'" @click="deleteUpload(`${index}`)"></i> <i class="fa fa-check fl-r" v-if="fileInfo.type === 'success'"></i>
                            <span v-if="fileInfo.type === 'fail'">
                              <i class="fa fa-minus-circle"></i>Error al cargar el archivo.
                              <i class="fa fa-close fl-l" @click="deleteUpload(`${index}`)"></i>
                          </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</template>
<script>
export default {
    data() {

        return {
            options: {
                className: 'btn-solid',
                btnContent: 'Agregar archivo',
                //url:"upload",
                //accept: 'image/png, image/jpg, image/jpeg, application/pdf, application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                autoStart: true,
                size: 26214400,
            },
            imageUrl: '',
            fileInfoList: []
        }
    },
    props: {
        id:Number,
        multiple: {
            type: Boolean,
            default: true
        }
    },
    methods: {
        fileSizeError(fileNames) {
            alert('El archivo excede los 25 MB: ', ...fileNames)
        },
        progressUpdate(fileInfoList) {
            this.fileInfoList = fileInfoList;
            //Se retorna al componente padre
            this.$emit('input', this.fileInfoList);
        },
        abortUpload(id, index) {
            //Se aborta la carga del archivo y se elimina su registro asociado en el vector
            this.$refs.fileUploadComp.abort(id)
            this.deleteUpload(index);
        },
        deleteUpload(index){
            //Se elimina del vector el archivo indicado
            this.fileInfoList.splice(index, 1);
        },
        startUpload(id) {
            this.$refs.fileUploadComp.startUpload(id)
        }
    },
}
</script>
<style scoped>
.overflow-hide {
    overflow: hidden;
}
.image-show-section {
    max-width: 450px;
    max-height: 400px;
    border-radius: 5px;
}
.section-title {
    margin: 0;
    font-weight: normal;
    text-align: left;
    line-height: 40px;
    text-indent: 20px;
    border-bottom: 1px solid #ddd;
}
</style>
