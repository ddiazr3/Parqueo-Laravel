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
                                    'form-control': true}"
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
                               :class="{'form-control' : true, 'is-invalid' : validationErrors['user.name']}">
                        <div v-if="validationErrors['user.name']" class="invalid-feedback">
                            {{ validationErrors['user.name'][0] }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="nombre">Descripción </label>
                        <input name="nombre" type="text"
                               v-model="ingreso.descripcion"
                               :class="{'form-control' : true, 'is-invalid' : validationErrors['user.name']}">
                        <div v-if="validationErrors['user.name']" class="invalid-feedback">
                            {{ validationErrors['user.name'][0] }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-2 ">
                    <button class="btn btn-primary mt-4" @click="save" :disabled="saving">Guardar</button>
                </div>
            </div>
        </div>

        <div class="card-body">

            <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Placa</th>
                    <th>Hora Ingreso</th>
                    <th>Hora Salida</th>
                    <th>Salir</th>
                </tr>
                </thead>
            </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Selectize from "vue2-selectize";

export default {
    props: ["tipoplacas", "ingresos"],
    data() {
        return {
            ingreso: {
                tipoplaca: 1,
                numeroplaca: null,
                descripcion: null
            },
            data: {
                user: null,
            },
            loading: true,
            saving: false,
            validationErrors: {
                user: null,
            },
        };
    },
    mounted() {

    },
    methods: {
        // save() {
        //     this.saving = true;
        //     axios
        //         .post("/profile", this.data)
        //         .then((response) => {
        //             window.location = "/profile";
        //         })
        //         .catch((error) => {
        //             this.handleError(error);
        //         });
        // },
        // handleError(error) {
        //     this.saving = false;
        //
        //     if (error.response.status == 422) {
        //         this.validationErrors = error.response.data.errors;
        //     } else {
        //         alert(error.response.data.message);
        //     }
        // },
    },
    components: {
        Selectize,
    },
};
</script>
