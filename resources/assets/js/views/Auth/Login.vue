<template>
    <form class="form" @submit.prevent="login">
        <h1 class="form_title">歡迎回來</h1>

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

        <!-- 按鈕區塊 -->
            <div class="form__group">
                <!-- v-bind -->
                <button :disabled="isProcessing" class="btn btn__primary">登入</button>
            </div>
        <!-- 按鈕區塊 end -->
    </form>
</template>

<script type="text/javascript">
    import Flash from '../../helpers/flash';
    import Auth from '../../store/auth';
    import { post } from '../../helpers/api';
    export default {
        data() {
            return {
                form: {
                    email: '',
                    password: '',
                },
                error: {},
                isProcessing: false
            }
        },
         methods: {
            login() {
                this.isProcessing = true
                this.error = {}
                post('api/login', this.form)
                    .then((res) => {
                        if(res.data.authenticated) {
                            Auth.set(res.data.api_token, res.data.user_id)
                            Flash.setSuccess('登入成功!')
                            this.$router.push('/')
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