<template>
    <div class="card">
        <div v-if="loading" class="text-center">
            <div class="card-body">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
            </div>
        </div>
        <template v-else>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group mb-2">
                            <label for="nombre">Empresa</label>
                            <input
                                name="nombre"
                                type="text"
                                :class="{
                                    'form-control': true,
                                    'is-invalid': validationErrors['user.empresa'],
                                }"
                                v-model="data.empresa.empresa"
                            />
                            <div v-if="validationErrors['empresa.empresa']" class="invalid-feedback">
                                {{ validationErrors["empresa.empresa"][0] }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group mb-2">
                            <label for="email">Dirección</label>
                            <input
                                name="email"
                                type="text"
                                autocomplete="off"
                                :class="{
                                    'form-control': true,
                                    'is-invalid': validationErrors['empresa.direccion'],
                                }"
                                v-model="data.empresa.direccion"
                            />
                            <div v-if="validationErrors['empresa.direccion']" class="invalid-feedback">
                                {{ validationErrors["empresa.direccion"][0] }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group mb-2">
                            <label for="email">Teléfono</label>
                            <input
                                name="email"
                                type="tel"
                                autocomplete="off"
                                :class="{
                                    'form-control': true,
                                    'is-invalid': validationErrors['empresa.telefono'],
                                }"
                                v-model="data.empresa.telefono"
                            />
                            <div v-if="validationErrors['empresa.telefono']" class="invalid-feedback">
                                {{ validationErrors["empresa.telefono"][0] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary" @click="save" :disabled="saving">Guardar</button>
            </div>
        </template>
    </div>
</template>

<script>
import axios from "axios";
import Selectize from "vue2-selectize";

export default {
    data() {
        return {
            data: {
                empresa: null
            },
            loading: true,
            saving: false,
            validationErrors: {
                empresa: null,
            },
        };
    },
    props: ["id", "path"],
    mounted() {
        axios
            .get(this.path + "/" + this.id + "/detail")
            .then((response) => {
                this.loading = false;
                this.data = response.data;
            })
            .catch((e) => {
                this.loading = false;
                alert(e);
            });
    },
    methods: {
        save() {
            this.saving = true;
            if (this.id != 0) {
                axios
                    .patch(this.path + "/" + this.id, this.data)
                    .then((response) => {
                        window.location = this.path;
                    })
                    .catch((error) => {
                        this.handleError(error);
                    });
            } else {
                axios
                    .post(this.path, this.data)
                    .then((response) => {
                        window.location = this.path;
                    })
                    .catch((error) => {
                        this.handleError(error);
                    });
            }
        },
        handleError(error) {
            this.saving = false;

            if (error.response.status == 422) {
                this.validationErrors = error.response.data.errors;
            } else {
                alert(error.response.data.message);
            }
        },
    },
    components: {
        Selectize,
    },
};
</script>
