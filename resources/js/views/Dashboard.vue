<template>
    <div v-if="verdashboard">
        <div class="card" v-for="emp in datosEmpresa">
            <div class="card-header">{{ emp.empresa.empresa }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-3">
                                <label>Año</label>
                                <selectize name="anios" class="form-control input-sm" v-model="emp.search.anio">
                                    <option v-for="an in anios" v-bind:value="an" v-text="an"></option>
                                </selectize>
                            </div>
                            <div class="col-sm-3">
                                <label>Mes</label>
                                <selectize name="anios" class="form-control input-sm" v-model="emp.search.mes">
                                    <option v-for="m in meses" :value="m.id" v-text="m.name"></option>
                                </selectize>
                            </div>
                            <div class="col-sm-2">
                                <label>Semana</label>
                                <selectize name="anios" class="form-control input-sm" v-model="emp.search.semana">
                                    <option v-for="s in semanas" :value="s" v-text="s"></option>
                                </selectize>
                            </div>
                            <div class="col-sm-2">
                                <label>Dia</label>
                                <selectize name="anios" class="form-control input-sm" v-model="emp.search.dia">
                                    <option v-for="d in dias" :value="d" v-text="d"></option>
                                </selectize>
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-success btn-sm mt-4" title="Buscar"
                                        @click="buscar(emp.search)"><i class="fa fa-search"></i></button>
                                <button class="btn btn-warning  btn-sm mt-4" title="Limpiar"
                                        @click="buscar(emp.search,1)"><i
                                    class="fa fa-broom"></i></button>
                            </div>

                        </div>
                        <!--                        <BarChart v-bind:chartData="emp.chartDataBar" v-bind:empresa="emp"></BarChart>-->
                        <Bar
                            :chart-options="{
                                    responsive: true
                                }"
                            :chart-data="emp.chartDataBar"
                            chart-id="bar-chart"
                            dataset-id-key="label"
                            :width="100"
                            :height="50"
                        />
                    </div>
                    <div class="col-sm-6">
                        <!-- <DoughnutChart v-bind:chartData="emp.chartDataDoung"></DoughnutChart>-->
                        <Doughnut
                            :chart-options="{
                                responsive: true,
                                maintainAspectRatio: false
                             }"
                            :chart-data="emp.chartDataDoung"
                            chart-id="doughnut-chart"
                            dataset-id-key="label"
                        />
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Selectize from "vue2-selectize";
import Sweetalert from "../sweetalert";

import {Doughnut, Bar} from 'vue-chartjs/legacy'

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale)

export default {
    props: ['verdashboard', 'anios', 'meses', 'semanas', 'dias', 'empresas'],
    mixins: [Sweetalert],

    components: {
        Selectize, Doughnut, Bar
    },
    data() {
        return {
            datosEmpresa: this.empresas,
            search: {
                anio: null,
                mes: null,
                semana: null,
                dia: null
            }
        };
    },
    mounted() {
        this.listen();
    },
    methods: {
        listen() {
            if (this.empresas.length) {
                this.empresas.forEach(emi => {
                    Echo.private('ingreso-ticket.' + emi.empresa.id)
                        .listen('IngresoTicketEvent', (e) => {
                            let data = this.datosEmpresa.find(el => el.empresa.id == e.ticket.empresa_id)
                            let index = this.datosEmpresa.findIndex(el => el.empresa.id == e.ticket.empresa_id)
                            if (data) {

                                let chartDataDoung = data.chartDataDoung.datasets[0].data[0]
                                data.chartDataDoung.datasets[0].data[0] = chartDataDoung + 1
                                //console.log(data)
                                this.$set(this.datosEmpresa, index, data)
                            }
                        });
                })
            }
        },
        limpiar() {
            this.search = {
                anio: null,
                mes: null,
                semana: null,
                dia: null
            }
        },
        async buscar(search, limpiar = 0) {
            if(limpiar == 0){
                let error = await this.validateBusqueda(search)
                if (error) {
                    this.alertError(this, {text: error})
                    return
                }
            }

            axios.post('/search/'+limpiar, {search: search})
                .then((resp) => {
                    var respuesta = resp.data[0]
                    var dataKey = this.datosEmpresa.findIndex(el => el.empresa.id == respuesta.empresa.id)
                    this.$set(this.datosEmpresa, dataKey, respuesta)
                }).catch((err) => {
                this.handleError(err);
            })
        },
        async validateBusqueda(search) {
            let error = null
            if (search.anio == null &&
                search.mes == null &&
                search.semana == null &&
                search.dia == null) {
                error = 'No esta filtrando por ningun campo.';
            }

            if (search.mes != null) {
                if (search.anio == null) {
                    error = 'Ingrese Año a filtrar.';
                }
            }
            if (search.semana != null) {
                if (search.mes != null) {
                    if (search.anio == null) {
                        error = 'Ingrese Año a filtrar.';
                    }
                } else {
                    error = 'Ingrese Mes a filtrar.';
                }
            }
            if (search.dia != null) {
                if (search.mes != null) {
                    if (search.anio == null) {
                        error = 'Ingrese Año a filtrar.';
                    }
                } else {
                    error = 'Ingrese Mes a filtrar.';
                }
            }
            return error
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
