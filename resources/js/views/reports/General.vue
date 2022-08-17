<template>

    <div class="card">
        <div v-if="loading" class="text-center">
            <div class="card-body">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
            </div>
        </div>
        <div class="card-header">
            <div class="row">
                <div class="col-sm-2">
                    <label>Placa</label>
                    <input type="text" class="form-control" v-model.trim="objectSeaarch.placa">
                </div>
                <div class="col-sm-2">
                    <label>Fecha Inicio</label>
                    <input type="date" class="form-control" v-model.trim="objectSeaarch.fechai">
                </div>
                <div class="col-sm-2">
                    <label>Fecha Fin</label>
                    <input type="date" class="form-control" v-model.trim="objectSeaarch.fechaf">
                </div>
                <div class="col-sm-2">
                    <label>Min. transcurridos despues de</label>
                    <input type="time" class="form-control" v-model.trim="objectSeaarch.min">
                </div>
                <div class="col-sm-2">
                    <label>Empresa</label>
                    <selectize
                        name="empresas"
                        multiple
                        v-model="objectSeaarch.empresa"
                    >
                        <option v-for="empresa in empresas" v-text="empresa.empresa" :value="empresa.id"></option>
                    </selectize>
                </div>
                <div class="col-sm-2">
                    <label>Acciones</label><br>
                    <button class="btn btn-info" title="Buscar" :disabled="saving" @click="buscar">
                        <i class="fa fa-search"></i>
                    </button>
                    <button class="btn btn-warning" title="Reiniciar" :disabled="saving" @click="reload">
                        <i class="fa fa-circle-notch"></i>
                    </button>
                    <button class="btn btn-success" title="Descargar Excel" :disabled="saving" @click="exportExcel">
                        <i class="fa fa-file-excel"></i>
                    </button>
                    <button class="btn btn-danger" title="Descargar Pdf" :disabled="saving" @click="exportPdf">
                        <i class="fa fa-file-pdf"></i>
                    </button>
                </div>

            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Empresa</th>
                            <th>Descripción</th>
                            <th>Placa</th>
                            <th>Fecha Ingreso</th>
                            <th>Fecha Egreso</th>
                            <th>Tiempo Transcurrido</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="d in data.data">
                            <td v-text="d.empresa.empresa"></td>
                            <td v-text="d.descripcion"></td>
                            <td v-text="d.placa"></td>
                            <td v-text="d.fecha_ingreso"></td>
                            <td v-text="d.fecha_egreso"></td>
                            <td>{{ timeSince(d.fecha_ingreso, d.fecha_egreso) }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <pagination
                        class="mb-0"
                        :data="data"
                        @pagination-change-page="getResults"
                        :limit="25"
                        :align="'right'"
                    />
                </div>
            </div>
        </div>
        <div class="card-footer">

        </div>
    </div>
</template>

<script>
import axios from "axios";
import Selectize from "vue2-selectize";
import Sweetalert from "../../sweetalert";
import LaravelVuePagination from '../../components/LaravelVuePagination'

var moment = require('moment')

export default {
    mixins: [Sweetalert],
    props: ["path", "empresas"],
    data() {
        moment.locale('es')
        return {
            objectSeaarch: {
                placa: '',
                fechai: '',
                fechaf: '',
                min: '',
                empresa: []
            },
            data: {},
            loading: false,
            saving: false,
            validationErrors: {
                user: null,
            },
            now: moment()
        };
    },
    components: {
        Selectize,
        'pagination': LaravelVuePagination
    },
    methods: {
        reload() {
            this.confirm(this, {
                title: '',
                showCancelButton: true,
                confirmButtonText: "Si",
                text: 'Seguro de recargar la pagina?',
            }).then((result) => {
                location.reload()
            })
        },
        buscar() {
            if (this.validateDataEmpy()) {
                this.alertError(this, {text: 'Ingrese un valor para el filtro '})
                return
            }
            if (this.objectSeaarch.empresa.length < 1) {
                this.alertError(this, {text: 'Ingrese al menos una empresa'})
                return
            }
            this.getResults()
        },
        exportExcel() {
            if (this.validateDataEmpy()) {
                this.alertError(this, {text: 'Ingrese un valor para el filtro '})
                return
            }
            if (this.objectSeaarch.empresa.length < 1) {
                this.alertError(this, {text: 'Ingrese al menos una empresa'})
                return
            }
            this.saving = true
            axios.post(`${this.path}/export-excel`, {
                datasearch: this.objectSeaarch
            }, {
                responseType: "arraybuffer",
            })
                .then((response) => {
                    this.loading = false;
                    var fileURL = window.URL.createObjectURL(
                        new Blob([response.data], {
                            type: "application/vnd.ms-excel;charset=utf-8",
                        })
                    );
                    var fileLink = document.createElement("a");
                    fileLink.href = fileURL;
                    fileLink.setAttribute("download", "reporte.xlsx");
                    document.body.appendChild(fileLink);
                    fileLink.click();
                    toastr.success("Descargado Exitosamente")
                })
                .catch((e) => {
                    this.loading = false;
                    this.handleError(error);

                })
                .finally(() => {
                    this.saving = false
                });


        },
        exportPdf() {
            if (this.validateDataEmpy()) {
                this.alertError(this, {text: 'Ingrese un valor para el filtro '})
                return
            }
            if (this.objectSeaarch.empresa.length < 1) {
                this.alertError(this, {text: 'Ingrese al menos una empresa'})
                return
            }
            this.saving = true
            axios.post(`${this.path}/export-pdf`, {
                datasearch: this.objectSeaarch
            }, {
                responseType: "arraybuffer",
            })
                .then((response) => {
                    console.log(response.data)
                    const blob = new Blob([response.data], {type: "application/pdf"});
                    const link = document.createElement("a");
                    link.href = window.URL.createObjectURL(blob);
                    link.download = "test.pdf";
                    link.click();
                    toastr.success("Descargado Exitosamente")
                })
                .catch((e) => {
                    this.loading = false;
                    this.handleError(error);
                })
                .finally(() => {
                    this.saving = false
                });
        },
        validateDataEmpy() {
            let error = false
            if (this.objectSeaarch.placa == '' &&
                this.objectSeaarch.fechai == '' &&
                this.objectSeaarch.fechaf == '' &&
                this.objectSeaarch.min == '' &&
                this.objectSeaarch.empresa.length < 1) {
                error = true

            }
            return error
        },
        getResults(page = 1) {
            this.saving = true
            axios.post(`${this.path}/search`, {
                datasearch: this.objectSeaarch,
                page: page
            })
                .then((response) => {
                    this.data = response.data
                    this.loading = false;
                })
                .catch((e) => {
                    this.loading = false;
                    this.handleError(error);
                })
                .finally(() => {
                    this.saving = false
                });
        },
        timeSince(fechai, fechaf) {
            let duration = ''
            if (fechaf) {
                let last = moment(fechai)
                let dif = last.diff(moment(fechaf))
                duration = moment.duration(dif).humanize(true)
            } else {
                if (this.now) {
                    let last = moment(fechai)
                    let dif = last.diff(this.now)
                    duration = moment.duration(dif).humanize(true)
                }
            }
            return duration
        },
        save() {
            this.saving = true;
            axios
                .post("/profile", this.data)
                .then((response) => {
                    window.location = "/profile";
                })
                .catch((error) => {
                    this.handleError(error);
                });
        },
        handleError(error) {
            this.saving = false;

            if (error.response != undefined) {
                if (error.response.status == 422) {
                    this.validationErrors = error.response.data.errors;
                } else {
                    this.alertError(this, {text: error.response.data.message})
                }
            } else {
                console.log(error)
            }
        },
    },
};
</script>
