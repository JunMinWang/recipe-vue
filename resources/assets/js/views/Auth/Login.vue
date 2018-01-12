<template>
<div>
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
                <button :disabled="isProcessing" class="btn btn__primary" @click.prevent="login_fb">臉書登入</button>
            </div>
        <!-- 按鈕區塊 end -->
    </form>
    
</div>
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
                fbAccessToken: '',
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
            },
            login_fb() {
                this.isProcessing = true
                
                FB.getLoginStatus((res) => {
                    
                    if(res.status === "connected") {
                        // 已經登入
                        console.log('已經登入')
                        this.fbAccessToken = res.authResponse.userID
                        this.getFbProfile()
                    } else {
                        // 尚未登入
                        console.log('尚未登入')
                        FB.login((res) => {
                            if(res.status === 'connected') {
                                console.log('剛剛未登入 但現在已登入')
                                this.fbAccessToken = res.authResponse.userID
                                this.getFbProfile()
                            } else {
                                console.log('都沒登入!')
                                this.isProcessing = false
                                return 
                            }
                        })
                    }
                })

                
            },
            getFbProfile() {
                FB.api('/me?fields=id,name,email', (response) => {
                    response.api_token = this.fbAccessToken
                    post('api/validate/facebook', response)
                        .then((res) => {
                            if(res.data.status === "validated") {
                                Auth.set(res.data.api_token, res.data.user_id)
                                Flash.setSuccess('登入成功!')
                                this.$router.push('/')
                            } else {
                                Flash.setError('登入失敗!')
                            }
                        })
                })
                this.isProcessing = false
            }
        }
    }
</script>