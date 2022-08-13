<template>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="email">T. Placa</label>
                        <selectize
                            name="empresas"
                            :class="{
                                    'form-control': true,
                                     'is-invalid': validationErrors['tipoplaca']
                        }"
                            v-model="ingreso.tipoplaca"
                        >
                            <option v-for="tipo in tipoplacas" :value="tipo.id">
                                {{ tipo.tipo }}
                            </option>
                        </selectize>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="nombre">Placa</label>
                        <input name="nombre" type="text"
                               v-model="ingreso.numeroplaca"
                               :class="{'form-control' : true, 'is-invalid' : validationErrors['numeroplaca']}">
                        <div v-if="validationErrors['numeroplaca']" class="invalid-feedback">
                            {{ validationErrors['numeroplaca'][0] }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="nombre">Descripción </label>
                        <input name="nombre" type="text"
                               v-model="ingreso.descripcion"
                               :class="{'form-control' : true}">

                    </div>
                </div>
                <div class="col-sm-2 ">
                    <button class="btn btn-primary mt-4" @click="save" :disabled="saving">Guardar</button>
                </div>
                <div class="col-sm-2">
                    <label for="nombre" class="mt-4">Cantidad de ingresos: {{ ticketscount }}</label>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped table-hover dataTable display">
                    <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>Descripción</th>
                        <th>Placa</th>
                        <th>Fecha Ingreso</th>
                        <th>Fecha Salida</th>
                        <th>Opc.</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="ing in tickets">
                        <td v-text="ing.empresa.empresa"></td>
                        <td v-text="ing.descripcion"></td>
                        <td v-text="ing.placa"></td>
                        <td v-text="ing.fecha_ingreso"></td>
                        <td v-text="ing.fecha_egreso"></td>
                        <td>
                            <button class="btn btn-warning btn-sm" @click="cancelar(ing.id)">
                                <i class="fa fa-stop"></i>
                            </button>
                            <button class="btn btn-primary btn-sm" @click="salir(ing.id)">
                                <i class="fa fa-car-side"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Selectize from "vue2-selectize";
import Sweetalert from "../sweetalert";

export default {
    props: ["tipoplacas", "ingresos", "path", "emid"],
    mixins: [Sweetalert],
    data() {
        return {
            ingreso: {
                tipoplaca: 1,
                numeroplaca: null,
                descripcion: null
            },
            tickets: this.ingresos,
            loading: true,
            saving: false,
            validationErrors: {},
        };
    },
    mounted() {
        // setTimeout(() => {

        //let channel = Echo.join('ingreso-ticket.'+this.id)
        // channel.here((data) => {
        //     //los que estan unidos cuando yo me registro
        //     console.log("estoy dentro de here")
        //     console.log(data)
        // }).joining((data) => {
        //     //quienes se unen
        //     console.log("estoy dentro de joining")
        //     console.log(data)
        // }).leaving((data) => {
        //     //Quienes avandona el canal
        //     console.log("estoy dentro de joining")
        //     console.log(data)
        // }).listen('IngresoTicketEvent', (e) => {
        //     console.log("eestoy escuchando")
        //     console.log(e)
        // })
        this.dataTableInicio()
        this.listen(this.emid)
        this.listen2(this.emid)
    },
    methods: {
        save() {
            this.saving = true;
            axios
                .post(this.path, this.ingreso)
                .then((response) => {
                    // this.ingresos.push(response.data)
                    this.tickets.push(response.data)
                    console.log("tamaño " + this.ingresos.length)
                    console.log(response.data);
                    this.clearSave()
                    this.saving = false;
                })
                .catch((error) => {
                    this.handleError(error);
                });
        },
        cancelar(id) {
            this.confirm(this,{
                title: '',
                showCancelButton: true,
                confirmButtonText: "Si",
                text: 'Seguro de cancelar este ingreso?',
            }).then((result) => {
                axios
                    .get(this.path+'/cancelar/'+id)
                    .then((response) => {
                        //$('.dataTable').dataTable().destroy();
                        $('.dataTable').dataTable().empty();
                        console.log("1")
                        let newtickets = this.tickets.filter((el) => {
                            if(el.id != id){
                                console.log("1.1")
                                return el
                            }
                        })
                        console.log("2")
                        this.tickets = newtickets
                        this.dataTableInicio()
                        console.log("3")
                        // $('.dataTable').dataTable({
                        //     data: newtickets
                        // });
                    })
                    .catch((error) => {
                        this.handleError(error);
                    });
            })
        },
        salir(id) {
            this.confirm(this,{
                title: '',
                showCancelButton: true,
                confirmButtonText: "Si",
                text: 'Seguro de este egreso?',
            }).then((result) => {
                axios
                    .get(this.path+'/salir/'+id)
                    .then((response) => {
                        let newtickets = this.tickets.filter((el) => {
                            if(el.id != id){
                                return el
                            }
                        })
                        this.tickets = newtickets
                    })
                    .catch((error) => {
                        this.handleError(error);
                    });
            })
        },
        clearSave() {
            this.ingreso = {
                tipoplaca: 1,
                numeroplaca: null,
                descripcion: null
            }
        },
        handleError(error) {
            this.saving = false;
            if(error.response != undefined){
                if (error.response.status == 422) {
                    this.validationErrors = error.response.data.errors;
                } else {
                    this.alertError(this, {text: error.response.data.message})
                }
            }else{
                console.log(error)
            }

        },
        errorClass(item) {
            return {
                "form-control": true,
                "is-invalid": this.validationErrors.hasOwnProperty(item),
            };
        },
        listen(emids) {
            if (emids.length) {
                emids.forEach(emi => {
                    Echo.private('ingreso-ticket.' + emi)
                        .listen('IngresoTicketEvent', (e) => {
                            this.ingresos.push(e.ticket)
                        });

                })
            }
        },
        listen2(emids){
            if (emids.length) {
                emids.forEach(emi => {
                    Echo.private('egreso-ticket.' + emi)
                        .listen('EgresoTicketEvent', (e) => {
                            let newtickets = this.tickets.filter((el) => {
                                if(el.id != e.ticket){
                                    return el
                                }
                            })
                            this.tickets = newtickets
                        });
                })
            }
        },
        dataTableInicio(){
            let table = $('.dataTable').dataTable({
                processing: true,
                // serverSide : true,
                searchDelay: 500,
                bLengthChange: false,
                sDom : '<"row" <"col-sm-8 pull-left"f> <"col-sm-4"<"btn-toolbar pull-right"  B <"btn-group btn-group-sm btn-group-add">>>>     t<"pull-left"i><"pull-right"p>',
                iDisplayLength: 20,
                columnDefs: [
                    {
                        targets: -1,
                        class: "text-right text-end",
                    },
                ],
                oLanguage: {
                    sLengthMenu: "Mostrar _MENU_ resultados por p&aacute;gina",
                    sZeroRecords: "No se encontraron registros",
                    sInfo: "Mostrando _START_ a _END_ de _TOTAL_ resultados",
                    sInfoEmpty: "Mostrando 0 a 0 de 0 resultados",
                    sInfoFiltered: "(filtrado de _MAX_ resultados totales)",
                    sSearch: "",
                    sProcessing: "Procesando",
                    oPaginate: {
                        sPrevious: "Anterior",
                        sNext: "Siguiente",
                        sFirst: "Primera",
                        sLast: "Ultima"
                    },
                },
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Buscar"
                }
            })

        }
    },
    computed: {
        ticketscount: function () {
            return this.tickets.length
        }
    },
    components: {
        Selectize,
    },
};
</script>
<style>
.dataTables_filter label {
    width: 100%;
}
.dataTables_filter label input {
    width: 100% !important;
}
</style>
