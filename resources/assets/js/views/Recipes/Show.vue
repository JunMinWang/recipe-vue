<template>
    <div class="recipe__show">
        <div class="recipe__row">
            <!-- 圖片區塊 -->
            <div class="recipe__image">
                <div class="recipe__box">
                    <img :src="`images/${recipe.image}`" v-if="recipe.image">
                </div>
            </div>
            <!-- 圖片區塊 end -->

            <!-- 詳細資料區塊 -->
            <div class="recipe__details">
                <div class="recipe__details_inner">
                    <small>由 {{ recipe.user.name }} 提供</small>
                    <h1 class="recipe__title">{{ recipe.name }}</h1>
                    <p class="recipe__description">{{ recipe.description }}</p>
                    <div v-if="auth.api_token && auth.user_id === recipe.user_id">
                        <router-link :to="`/recipes/${recipe.id}/edit`" class="btn btn__primary">
                            編輯
                        </router-link>
                        <button class="btn btn__danger" @click="remove" :disable="isRemoving">刪除</button>
                    </div>
                </div>
            </div>
            <!-- 詳細資料區塊 end -->
        </div>

        <div class="recipe__row">
            <!-- 食材區塊 -->
            <div class="recipe__ingredients">
                <div class="recipe__box">
                    <h3 class="recipe__sub_title">食材清單</h3>
                    <ul>
                        <li v-for="ingredient in recipe.ingredients">
                            <span>{{ ingredient.name }}</span>
                            <span>{{ ingredient.qty }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- 食材區塊 end -->

            <!-- 作法區塊 -->
            <div class="recipe__directions">
                <div class="recipe__directions_inner">
                    <h3 class="recipe__sub_title">作法</h3>
                    <ul>
                        <li v-for="(direction, i) in recipe.directions">
                            <p>
                                <strong>{{ i+1 }}</strong>
                                {{ direction.description }}
                            </p>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- 作法區塊 end -->
        </div>
    </div>
</template>

<script type="text/javascript">
    import Flash from '../../helpers/flash'
    import Auth from '../../store/auth'
    import { get, post, del } from '../../helpers/api'
    export default {
        data() {
            return {
                auth: Auth.state,
                isRemoving: false,
                recipe: {
                    user: {},
                    ingredients: [],
                    directions: []
                }
            }
        },
        created() {
            get(`api/recipes/${this.$route.params.id}`)
                .then((res) => {
                    this.recipe = res.data.recipe
                })
        },
        methods: {
            remove() {
                this.isRemoving = false
                del(`api/recipes/${this.$route.params.id}`)
                    .then((res) => {
                        if(res.data.deleted) {
                            Flash.setSuccess('已成功刪除')
                            this.$router.push('/')
                        }
                    })
            }
        }
    }
</script>