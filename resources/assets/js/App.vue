<template>
    <div class="container">
        <div class="navbar">
            <!-- 左上角 -->
                <div class="navbar__brand">
                    <router-link to="/">Recipe</router-link>
                </div>
            <!-- 左上角 結束 -->

            <!-- 右上角 -->
                <ul class="navbar__list">
                    <li class="navbar__item" v-if="!check">
                        <router-link to="/login">登入</router-link>
                    </li>
                    <li class="navbar__item" v-if="!check">
                        <router-link to="/register">註冊</router-link>
                    </li>
                    <li class="navbar__item" v-if="check">
                        <router-link to="/recipes/create">新增食譜</router-link>
                    </li>
                    <li class="navbar__item" v-if="check">
                        <a @click.stop="logout">登出</a>
                    </li>
                </ul>
            <!-- 右上角 結束 -->
        </div>

        <!-- 成功訊息框 -->
            <div class="flash flash__success" v-if="flash.success">
                {{ flash.success }}
            </div>
        <!-- 成功訊息框 結束 -->

        <!-- 失敗訊息框 -->
            <div class="flash flash__error" v-if="flash.error">
                {{ flash.error }}
            </div>
        <!-- 失敗訊息框 結束 -->

        <!-- 路由 -->
            <router-view></router-view>
        <!-- 路由 結束 -->
    </div>
</template>

<script type="text/javascript">
    import Flash from './helpers/flash'
    import Auth from './store/auth'
    import { post } from './helpers/api'
    export default {
        created() {
            Auth.initialize()
        },
        data() {
            return {
                flash: Flash.state,
                auth: Auth.state
            }
        },
        computed: {
            check() {
                if( this.auth.api_token && this.auth.user_id) {
                    return true
                }
                return false
            }
        },
        methods: {
            logout() {
                post(`api/logout`)
                    .then((res) => {
                        if(res.data.logged_out) {
                            Auth.remove()
                            Flash.setSuccess('您已成功登出')
                            this.$router.push('/login')
                        }
                    })
            }
        }
    }
</script>