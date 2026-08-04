import React from 'react';
import { Navbar, Container, Nav } from 'react-bootstrap';
import { Link, useNavigate } from 'react-router-dom';
import './Navbar.css';

const NavbarTop = () => {
  const navigate = useNavigate();
  const user = JSON.parse(localStorage.getItem('user'));
  const isAdmin = user?.role === 'ADMIN';

  const handleLogout = () => {
    localStorage.removeItem('user');
    localStorage.removeItem('token');
    navigate('/');
  };

  return (
    <Navbar bg="primary" expand="lg" className="navbar-dark">
      <Container>
        <Navbar.Brand as={Link} to="/" className="fw-bold">
          🛡️ Warranty Manager
        </Navbar.Brand>
        <Navbar.Toggle aria-controls="basic-navbar-nav" />
        <Navbar.Collapse id="basic-navbar-nav">
          <Nav className="ms-auto">
            {!user ? (
              <>
                <Nav.Link as={Link} to="/login">Login</Nav.Link>
                <Nav.Link as={Link} to="/register">Register</Nav.Link>
              </>
            ) : (
              <>
                <Nav.Link as={Link} to="/dashboard">Dashboard</Nav.Link>
                <Nav.Link as={Link} to="/products">Products</Nav.Link>
                <Nav.Link as={Link} to="/warranty">Warranty</Nav.Link>
                <Nav.Link as={Link} to="/claims">Claims</Nav.Link>
                {isAdmin && (
                  <>
                    <Nav.Link as={Link} to="/admin">Admin</Nav.Link>
                    <Nav.Link as={Link} to="/admin/users">Users</Nav.Link>
                    <Nav.Link as={Link} to="/admin/products">Manage Products</Nav.Link>
                    <Nav.Link as={Link} to="/admin/claims">Manage Claims</Nav.Link>
                  </>
                )}
                <Nav.Link as={Link} to="/profile">Profile</Nav.Link>
                <Nav.Link onClick={handleLogout} className="cursor-pointer">Logout</Nav.Link>
              </>
            )}
          </Nav>
        </Navbar.Collapse>
      </Container>
    </Navbar>
  );
};

export default NavbarTop;
