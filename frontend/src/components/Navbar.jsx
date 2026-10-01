import React from 'react';
import { Navbar, Container, Nav, Badge } from 'react-bootstrap';
import { Link, useNavigate } from 'react-router-dom';
import './Navbar.css';

const NavbarTop = () => {
  const navigate = useNavigate();
  const user = JSON.parse(localStorage.getItem('user') || 'null');
  const isAdmin = user?.role === 'ADMIN';
  const isCustomer = user?.role === 'CUSTOMER' || user?.role === 'USER';

  const handleLogout = () => {
    localStorage.removeItem('user');
    localStorage.removeItem('token');
    navigate('/');
    window.location.reload();
  };

  return (
    <Navbar bg="primary" expand="lg" className="navbar-dark shadow-sm sticky-top">
      <Container>
        <Navbar.Brand as={Link} to="/" className="fw-bold d-flex align-items-center gap-2">
          <span className="fs-4">🛡️</span>
          <span>Warranty Hub</span>
        </Navbar.Brand>
        <Navbar.Toggle aria-controls="basic-navbar-nav" />
        <Navbar.Collapse id="basic-navbar-nav">
          <Nav className="ms-auto align-items-center gap-1">
            {!user ? (
              <>
                <Nav.Link as={Link} to="/">Home</Nav.Link>
                <Nav.Link as={Link} to="/login">Login</Nav.Link>
                <Nav.Link as={Link} to="/register">Register</Nav.Link>
              </>
            ) : (
              <>
                {!isAdmin && (
                  <>
                    <Nav.Link as={Link} to="/dashboard">Dashboard</Nav.Link>
                    <Nav.Link as={Link} to="/products">Products</Nav.Link>
                    <Nav.Link as={Link} to="/warranty">Warranty</Nav.Link>
                    <Nav.Link as={Link} to="/claims">Claims</Nav.Link>
                  </>
                )}
                {isAdmin && (
                  <>
                    <Nav.Link as={Link} to="/admin">Dashboard</Nav.Link>
                    <Nav.Link as={Link} to="/admin/users">Users</Nav.Link>
                    <Nav.Link as={Link} to="/admin/products">Products</Nav.Link>
                    <Nav.Link as={Link} to="/admin/claims">Claims</Nav.Link>
                  </>
                )}
                <Nav.Link as={Link} to="/profile">Profile</Nav.Link>
                <Badge bg="light" text="dark" className="ms-1 me-2">
                  {isAdmin ? 'ADMIN' : isCustomer ? 'CUSTOMER' : 'CUSTOMER'}
                </Badge>
                <Nav.Link onClick={handleLogout} className="cursor-pointer text-white">Logout</Nav.Link>
              </>
            )}
          </Nav>
        </Navbar.Collapse>
      </Container>
    </Navbar>
  );
};

export default NavbarTop;
