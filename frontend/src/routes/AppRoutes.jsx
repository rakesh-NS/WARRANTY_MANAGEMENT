import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import HomePage from '../pages/HomePage';
import LoginPage from '../pages/LoginPage';
import RegisterPage from '../pages/RegisterPage';
import DashboardPage from '../pages/DashboardPage';
import ProductListPage from '../pages/ProductListPage';
import AddProductPage from '../pages/AddProductPage';
import EditProductPage from '../pages/EditProductPage';
import WarrantyDetailsPage from '../pages/WarrantyDetailsPage';
import ClaimHistoryPage from '../pages/ClaimHistoryPage';
import AdminDashboardPage from '../pages/AdminDashboardPage';
import ManageUsersPage from '../pages/ManageUsersPage';
import ManageProductsPage from '../pages/ManageProductsPage';
import ManageClaimsPage from '../pages/ManageClaimsPage';
import ProfilePage from '../pages/ProfilePage';

const AppRoutes = () => (
  <Routes>
    <Route path="/" element={<HomePage />} />
    <Route path="/login" element={<LoginPage />} />
    <Route path="/register" element={<RegisterPage />} />
    <Route path="/dashboard" element={<DashboardPage />} />
    <Route path="/products" element={<ProductListPage />} />
    <Route path="/products/add" element={<AddProductPage />} />
    <Route path="/products/edit/:id" element={<EditProductPage />} />
    <Route path="/warranty" element={<WarrantyDetailsPage />} />
    <Route path="/claims" element={<ClaimHistoryPage />} />
    <Route path="/admin" element={<AdminDashboardPage />} />
    <Route path="/admin/users" element={<ManageUsersPage />} />
    <Route path="/admin/products" element={<ManageProductsPage />} />
    <Route path="/admin/claims" element={<ManageClaimsPage />} />
    <Route path="/profile" element={<ProfilePage />} />
    <Route path="*" element={<Navigate to="/" replace />} />
  </Routes>
);

export default AppRoutes;
