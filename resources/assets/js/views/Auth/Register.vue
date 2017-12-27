<template>
    <form class="form" @submit.prevent="register">
        <h1 class="form_title">建立帳號</h1>

        <!-- 姓名區塊 -->
            <div class="form__group">
                <label>姓名</label>
                <input type="text" class="form__control" v-model="form.name">
                <small class="error__control" v-if="error.name">{{error.name[0]}}</small>
            </div>
        <!-- 姓名區塊 end -->

        <!-- 電子信箱區塊 -->
            <div class="form__group">
                <label>電子信箱</label>
                <input type="text" class="form__control" v-model="form.email">
                <small class="error__control" v-if="error.email">{{ error.email[0] }}</small>
            </div>
        <!-- 電子信箱區塊 end -->

        <!-- 密碼區塊 -->
            <div class="form__group">
                <label>密碼</label>
                <input type="password" class="form__control" v-model="form.password">
                <small class="error__control" v-if="error.password">{{ error.password[0] }}</small>
            </div>
        <!-- 密碼區塊 end -->

        <!-- 確認密碼區塊 -->
            <div class="form__group">
                <label>確認密碼</label>
                <input type="password" class="form__control" v-model="form.password_confirmation">
                <small class="error__control" v-if="error.password_confirmation">{{ error.password_confirmation }}</small>
            </div>
        <!-- 確認密碼區塊 end -->

        <!-- 按鈕區塊 -->
            <div class="form__group">
                <!-- v-bind -->
                <button :disabled="isProcessing" class="btn btn__primary">註冊</button>
            </div>
        <!-- 按鈕區塊 end -->
    </form>
</template>

<script type="text/javascript">
    import Flash from '../../helpers/flash';
    import { post } from '../../helpers/api';
    export default {
        data() {
            return {
                form: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: ''
                },
                error: {},
                isProcessing: false
            }
        },
         methods: {
            register() {
                this.isProcessing = true
                this.error = {}
                post('api/register', this.form)
                    .then((res) => {
                        if(res.data.registered) {
                            Flash.setSuccess('會員註冊成功!')
                            this.$router.push('/login')
                        }
                        this.isProcessing = false
                    })
                    .catch((err) => {
                        if(err.response.status === 422) {
                            this.error = err.response.data.errors
                        }
                        this.isProcessing = false
                    })
            }
        }
    }
</script>