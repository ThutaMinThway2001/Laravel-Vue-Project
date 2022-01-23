<template>
    <div class="container my-5">
        <div class="row justify-content-end mb-3">
            <div class="col-md-4">
                <button class="btn btn-primary" @click.prevent="create">
                    <i class="fas fa-plus-circle me-1"></i> Create
                </button>
            </div>
            <div class="col-md-4 ">
                <form action="" @submit.prevent="view">
                    <div class="input-group">
                        <input type="text" placeholder="search" class="form-control" v-model="search">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-dark text-white ">
                        <h4>{{ isEdit? 'Edit' :'Create'}}</h4>
                    </div>
                    <div class="card-body">
                        <form action="" @submit.prevent="isEdit? updateProduct() : storeProduct()">
                            <AlertError :form="product" :message="message" />
                            <div class="form-group">
                                <label for="">Name</label>
                                <input type="text" class="form-control" v-model="product.name">
                                <div class="text-danger" v-if="product.errors.has('name')" v-html="product.errors.get('name')" />
                            </div>
                            <div class="form-group">
                                <label for="">Detail</label>
                                <input type="text" class="form-control" v-model="product.detail">
                                <div class="text-danger" v-if="product.errors.has('detail')" v-html="product.errors.get('detail')" />
                            </div>
                            <button type="submit" class="btn btn-success btn-sm mt-3 float-end">
                                <i class="far fa-save me-1"></i>Save
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <table class="table">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Detail</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in products.data" :key="product.id">
                            <td>{{product.id}}</td>
                            <td>{{product.name}}</td>
                            <td>{{product.detail}}</td>
                            <td>
                                <button @click.prevent="editProduct(product)" class="btn btn-warning btn-sm">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm" @click.prevent="deleteProduct(product.id)">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <pagination :data="products" @pagination-change-page="view" class="mt-3"></pagination>
                    <loading :active.sync="isLoading" ></loading>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import Form from 'vform' 
// Import component
import Loading from 'vue-loading-overlay';
// Import stylesheet
import 'vue-loading-overlay/dist/vue-loading.css';

import { Button, HasError, AlertError } from 'vform/src/components/bootstrap5'

export default {
    components: {
        Button, HasError, AlertError
    },
    name: "ProductComponent",
    data(){
        return{
            isLoading: false,
            message: '',
            search : '',
            products : {},
            isEdit : false,
            product : new Form({
                id: '',
                name: '',
                detail: ''
            })
        }
    },
    components: {
            Loading
        },
    methods: {
        view(page = 1){
            this.isLoading = true;
            this.$Progress.start()
            axios.get(`http://127.0.0.1:8000/api/products?page=${page}&search=${this.search}`)
        .then((response) => {
            this.products = response.data;
            this.$Progress.finish()
            this.isLoading = false;
        }).catch((err) => {
            this.$Progress.fail()
        });
        },
        storeProduct(){
            this.product.post('http://127.0.0.1:8000/api/products/')
            .then(response => {
                this.view();
                this.product.reset();
                Toast.fire({
                    icon: 'success',
                    title: 'Created in successfully'
                    })
            })
            .catch((err) => {
                this.message = err.response.data.message;
            })
        },
        editProduct(product){
            this.product.clear();
            this.isEdit = true;
            this.product.id = product.id;
            this.product.name = product.name;
            this.product.detail = product.detail;
            this.product.fill(product);
        },
        create(){
            this.product.clear();
            this.isEdit = false;
            this.product.reset();
        },
        updateProduct(){
            axios.put('api/products/' + this.product.id, this.product)
            .then(response => {
                this.view();
                this.product.reset();
                Toast.fire({
                    icon: 'success',
                    title: 'Created in successfully'
                    })
            });
        },
        deleteProduct(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                    axios.delete('api/products/' + id)
                    .then((response) => {
                        this.view()
                        Swal.fire({title: 'Deleted!', icon: 'success'})
                        Toast.fire({
                            icon: 'success',
                            title: 'Deleted in successfully'
                            })
                    })
                }
                })
            // if(!confirm("Are you sure to delete")){
            //     return false;
            // }
        }
    },
    created(){
        this.view();
    },
}
</script>

<style>

</style>