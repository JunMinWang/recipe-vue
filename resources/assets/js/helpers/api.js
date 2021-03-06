import axios from 'axios'
import Auth from '../store/auth'

export function get(url) {
    return axios({
        method: 'GET',
        url: url,
        headers: {
            // 這是什麼鬼?
            'Authorization': `Bearer ${ Auth.state.api_token }`
        }
    })
}

export function post(url, data) {
    return axios({
        method: 'POST',
        url: url,
        data: data,
        headers: {
            // 這是什麼鬼?
            'Authorization': `Bearer ${ Auth.state.api_token }`
        }
    })
}

export function del(url) {
    return axios({
        method: 'DELETE',
        url: url,
        headers: {
            // 這是什麼鬼?
            'Authorization': `Bearer ${ Auth.state.api_token }`
        }
    })
}