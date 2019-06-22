
const state = {
    user: null
};
const getters = {
    // 真偽値を確実に返すため二重否定
    /*
       var a = null;
       !a  -> true
       !!a -> false
     */
    check: state => !! state.user,
    username: state => state.user ? state.user.name : ''
};
const mutations = {
    setUser(state, user) {
        state.user = user;
    }
};
const actions = {
    async register(context, data) {
        const response = await axios.post('/api/register', data);
        context.commit('setUser', response.data);
    },
    async login(context, data) {
        const response = await axios.post('/api/login', data);
        context.commit('setUser', response.data);
    },
    async logout(context) {
        const resopnse = await axios.post('/api/logout');
        context.commit('setUser', null);
    }
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
};


