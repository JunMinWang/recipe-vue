<template>
    <div class="recipe__show">
        <div class="recipe__header">
            <h3>{{ action }} Recipe</h3>
            <div>
                <button class="btn btn__primary" @click="save" :disabled="isProcessing">
                    存檔
                </button>
                <button class="btn" @click="$router.back()" :disabled="isProcessing">
                    取消
                </button>
            </div>
        </div>

        <div class="recipe__row">
            <div class="recipe__image">
                <div class="recipe__box">
                    <image-upload v-model="form.image"></image-upload>
                    <small class="error__control" v-if="error.image">
                        {{ error.image[0] }}
                    </small>
                </div>
            </div>
            <div class="recipe__details">
                <div class="recipe__details_inner">
                    <!-- 姓名區塊 -->
                        <div class="form-group">
                            <label>姓名</label>
                            <input type="text" class="form__control" v-model="form.name">
                            <small class="error__control" v-if="error.name">
                                {{ error.name[0] }}
                            </small>
                        </div>
                    <!-- 姓名區塊 結束 -->

                    <!-- 描述區塊 -->
                        <div class="form-group">
                            <label>描述</label>
                            <textarea class="form__control" v-model="form.description"></textarea>
                            <small class="error__control" v-if="error.description">
                                {{ error.description[0] }}
                            </small>
                        </div>
                    <!-- 描述區塊 結束 -->
                </div>
            </div>
        </div>

        <div class="recipe__row">
            <!-- 食材區塊 -->
                <div class="recipe__ingredients">
                    <div class="recipe__box">
                        <h3 class="recipe__sub_title">食材</h3>
                        <div v-for="(ingredient, index) in form.ingredients" class="recipe__form">
                            <input type="text" class="form__control" v-model="ingredient.name" :class="[error[`ingredients.${index}.name`] ? 'error__bg' : '']">
                            <input type="text" class="form__control form_qty" v-model="ingredient.qty" :class="[error[`ingredients.${index}.qty`] ? 'error__bg' : '']">
                            <button class="btn btn__danger" @click="remove('ingredients', index)"> &times; </button>
                        </div>

                        <div class="btn" @click="addIngredient">新增食材</div>
                    </div>
                </div>
            <!-- 食材區塊 end -->

            <!-- 作法區塊 -->
                <div class="recipe__directions">
                    <div class="recipe__directions_inner">
                        <h3 class="recipe__sub_title">作法</h3>
                        <div v-for="(direction, index) in form.directions" class="recipe__form">
                            <textarea class="form__control" v-model="direction.description" :class="[error[`directions.${index}.description`] ? 'error__bg' : '']"></textarea>
                            <button class="btn btn__danger" @click="remove('directions', index)"> &times; </button>
                        </div>

                        <div class="btn" @click="addDirection">新增作法</div>
                    </div>
                </div>
            <!-- 作法區塊 end -->
        </div>
    </div>
</template>

<script type="text/javascript">
    import Vue from 'vue'
    import Flash from '../../helpers/flash'
    import { get, post } from '../../helpers/api'
    import { toMulipartedForm } from '../../helpers/form'
    import ImageUpload from '../../components/ImageUpload.vue'

    export default {
        components: {
            ImageUpload
        },
        data() {
            return {
                form: {
                    ingredients: [],
                    directions: []
                },
                error: {},
                isProcessing: false,
                initializeURL: `api/recipes/create`,
                storeURL: `api/recipes`,
                action: 'Create'
            }
        },
        created() {
            if(this.$route.meta.mode === 'edit') {
                this.initializeURL = `api/recipes/${this.$route.params.id}/edit`
                this.storeURL = `api/recipes/${this.$route.params.id}?_method=PUT`
                this.action = 'Update'
            }

            get(this.initializeURL)
                .then((res) => {
                    // (varName, key, value)
                    // 等同於this.$set()，用來指定Vue實例data中物件的值
                    Vue.set(this.$data, 'form', res.data.form)
                })
        },

        methods: {
            save() {
                this.isProcessing = true
                const form = toMulipartedForm(this.form, this.$route.meta.mode)

                post(this.storeURL, form)
                    .then((res) => {
                        if(res.data.saved) {
                            Flash.setSuccess(res.data.message)
                            this.$router.push(`/recipes/${res.data.id}`)
                        }
                    })
                    .catch((err) => {
                        if(err.response.status === 422) {
                            this.error = err.response.data.errors
                        }
                        this.isProcessing = false;
                    })
            },
            addDirection() {
                this.form.directions.push({description: ''})
            },
            addIngredient() {
                this.form.ingredients.push({
                    name: '',
                    qty: ''
                })
            },
            remove(type, index) {
                if(this.form[type].length > 1) {
                    this.form[type].splice(index, 1)
                    // splice方法是用來新增或移除陣列元素，(增加/移除項目的位置, 刪除的項目數量, 向陣列新增的項目)
                }
            }
        }
    }
</script>