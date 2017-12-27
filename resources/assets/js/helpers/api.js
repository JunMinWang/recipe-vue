import axios from 'axios'
import Auth from '../store/auth'

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