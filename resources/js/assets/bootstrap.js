window._ = require('lodash');

window.axios = require('axios').default;
window.axios.defaults.headers.common['X-Requested-With'] = document.querySelector('meta[name="csrf-token"]').content;
window.axios.defaults.headers.common['Content-Type'] = 'application/json';
window.axios.defaults.headers.common['Authorization'] = `Bearer ${document.querySelector('meta[name="bearer"]').content}`;
