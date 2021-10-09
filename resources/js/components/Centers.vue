<template>
    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">لیست مراکز تابعه</h3>

                        <div class="card-tools">
                            <div class="btn btn-success" @click="newModal"><i class="fa fa-home fa-fw"></i>ایجاد مرکز</div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                            <tr>
                                <th>کد</th>
                                <th>نام مرکز</th>
                                <th>سطح سازمانی</th>
                                <th> مرکز بالادستی</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr  v-for="center in centers" :key="center.id">
                                <td>{{ center.id }}</td>
                                <td>{{ center.name }}</td>
                                <td>{{ center.level }}</td>
                                <td>{{ center.parent}}</td>
                                <td>
                                    <i class="fa fa-edit blue" @click="editModal(center)"></i>
                                    /
                                    <i class="fa fa-trash red" @click="deleteCenter(center.id)"></i>

                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNewLabel"  v-show="!editMode">ایجاد مرکز</h5>
                        <h5 class="modal-title" id="addNewLabel"  v-show="editMode">ویرایش مرکز</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editMode? updateCenter() : createCenter()" >
                        <div class="modal-body">

                            <!-- //////////////// -->
                            <div class="form-group">
                                <input v-model="form.name" type="text" name="name" placeholder=" نام مرکز"
                                       class="form-control" :class="{'is-invalid':form.errors.has('name')}">

                                <div v-if="form.errors.has('name')" v-html="form.errors.get('name')" />
                            </div>
                            <!-- //////////////// -->
                            <div class="form-group">
                                <select name="level" id="level" v-model="form.level" class="form-control"
                                        :class="{'is-invalid':form.errors.has('level')}">
                                    <option value=""> سطح سازمانی</option>
                                    <option value="شبکه">شبکه</option>
                                    <option value="مرکز">مرکز</option>
                                    <option value="پایگاه">پایگاه</option>
                                    <option value="خانه">خانه</option>
                                </select>
                                <div v-if="form.errors.has('level')" v-html="form.errors.get('level')" />
                            </div>
                            <!-- //////////////// -->
                            <div class="form-group">
                                <select name="parent" id="parent" v-model="form.parent" class="form-control"
                                        :class="{'is-invalid':form.errors.has('parent')}">

                                  <option  v-for="center in centers"  :value="center.name" v-show="center.level=='مرکز'||center.level=='شبکه'"  >{{center.name}}</option>
                                </select>
                                <div v-if="form.errors.has('parent')" v-html="form.errors.get('parent')" />
                            </div>
                            <!-- //////////////// -->

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                            <button type="submit" v-show="editMode" class="btn btn-success">ویرایش</button>
                            <button type="submit" v-show="!editMode" class="btn btn-primary">ایجاد</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                editMode: false,
                centers: {},
                // create a new form
                form: new Form({
                    id: '',
                    name: '',
                    level: '',
                    parent: ''
                })
            }
        },
        methods: {
            updateCenter(){
                this.$Progress.start()
                this.form.put('api/centers/'+this.form.id)
                    .then(()=>{
                        Swal.fire(
                            'ویرایش!',
                            'مرکز مورد نظر ویرایش شد.',
                            'success'
                        )
                        Fire.$emit('Reload');
                        $('#addNew').modal('hide');
                        this.$Progress.finish()
                    })
                    .catch(()=>{
                        this.$Progress.fail()
                        Swal.fire(
                            'عدم ویرایش !',
                            'مرکز مد نظر ویرایش نشد.',
                            'error'
                        )
                        Fire.$emit('Reload');
                    })
            },
            editModal(center){
                this.form.reset();
                this.editMode=true;
                $('#addNew').modal('show');
                this.form.fill(center);
            },newModal(){
                this.editMode=false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            deleteCenter(id){
                Swal.fire({
                    title: 'آیا مطمئن هستید?',
                    text: "این اطلاعات دیگر قابل بازگشت نخواهد بود!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'بله حذف کن!',
                    cancelButtonText:'انصراف'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.form.delete('api/centers/'+id).then(()=>{
                            Swal.fire(
                                'حذف!',
                                'مرکز با موفقیت حذف شد',
                                'success'
                            )
                            Fire.$emit('Reload');
                        }).catch(()=>{
                            Swal.fire(
                                ' حذف !',
                                'مرکز مد نظر حذف نشد.',
                                'error'
                            )
                        });

                    }
                })

            },
            loadCenter(){

                axios.get('api/centers').then(({data})=>(this.centers=data.data));
            },
            createCenter () {
                this.$Progress.start()
                this.form.post('api/centers').then(()=>{
                    this.form.reset();
                    $('#addNew').modal('hide');
                    //this.loadUser();
                    Fire.$emit('Reload');
                    this.$Progress.finish();
                    Toast.fire({
                        icon: 'success',
                        title: 'مرکز با موفقیت ایجاد شد'
                    })
                });

            }
        },
        created() {
            this.loadCenter();
            Fire.$on('Reload',()=>{
                this.loadCenter();
            });
            // setInterval(()=>{this.loadUser()},300000);
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
