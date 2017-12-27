<template>
    <div class="container">
        <div class="navbar">
            <div class="navbar__brand">
                <router-link to="/">Recipe</router-link>
            </div>
            <ul class="navbar__list">
                <li class="navbar__item">
                    <router-link to="/login">Login</router-link>
                </li>
                <li class="navbar__item">
                    <router-link to="/register">Register</router-link>
                </li>
                <li class="navbar__item">
                    <router-link to="/logout">Logout</router-link>
                </li>
            </ul>
        </div>
        
        <router-view></router-view>
    </div>
</template>