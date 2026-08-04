import api from './api';

export const userService = {
  register: (data) => api.post('/users/register', data),
  login: (data) => api.post('/users/login', data),
  getProfile: (id) => api.get(`/users/${id}`),
  updateProfile: (id, data) => api.put(`/users/${id}`, data),
};

export const adminService = {
  login: (data) => api.post('/admin/login', data),
  getDashboard: () => api.get('/admin/dashboard'),
};

export const productService = {
  getAll: () => api.get('/products'),
  getById: (id) => api.get(`/products/${id}`),
  create: (data) => api.post('/products', data),
  update: (id, data) => api.put(`/products/${id}`, data),
  delete: (id) => api.delete(`/products/${id}`),
  search: (query) => api.get(`/products/search?q=${query}`),
};

export const warrantyService = {
  getAll: () => api.get('/warranty'),
  create: (data) => api.post('/warranty', data),
  update: (id, data) => api.put(`/warranty/${id}`, data),
};

export const claimService = {
  submit: (data) => api.post('/claims', data),
  getAll: () => api.get('/claims'),
  updateStatus: (id, status, adminRemarks) =>
    api.put(`/claims/${id}?status=${status}&adminRemarks=${adminRemarks || ''}`),
};
