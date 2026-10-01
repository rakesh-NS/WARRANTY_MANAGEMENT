import React, { useState } from 'react';
import { Container, Form, Button, Card, Alert, Row, Col } from 'react-bootstrap';
import { useNavigate } from 'react-router-dom';
import { userService } from '../services/serviceApi';

const demoAccounts = {
  user: { email: 'demo.user@example.com', password: 'demo123', role: 'CUSTOMER' },
  admin: { email: 'admin@example.com', password: 'admin123', role: 'ADMIN' },
};

const LoginPage = () => {
  const [formData, setFormData] = useState({ email: '', password: '' });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleDemoLogin = async (accountKey) => {
    const account = demoAccounts[accountKey];
    setFormData({ email: account.email, password: account.password });
    setError('');
    setLoading(true);
    try {
      const response = await userService.login({ email: account.email, password: account.password });
      localStorage.setItem('user', JSON.stringify(response.data));
      localStorage.setItem('token', response.data.token || 'demo-token');
      navigate(response.data.role === 'ADMIN' ? '/admin' : '/dashboard');
    } catch (err) {
      setError(err.response?.data?.message || 'Demo login failed');
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      const response = await userService.login(formData);
      localStorage.setItem('user', JSON.stringify(response.data));
      localStorage.setItem('token', response.data.token || 'demo-token');
      navigate(response.data.role === 'ADMIN' ? '/admin' : '/dashboard');
    } catch (err) {
      setError(err.response?.data?.message || 'Login failed');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Container className="py-5">
      <div className="row justify-content-center">
        <div className="col-md-5">
          <Card className="shadow">
            <Card.Body className="p-5">
              <h2 className="text-center mb-4">Welcome back</h2>
              {error && <Alert variant="danger">{error}</Alert>}
              <Form onSubmit={handleSubmit}>
                <Form.Group className="mb-3">
                  <Form.Label>Email</Form.Label>
                  <Form.Control
                    type="email"
                    name="email"
                    value={formData.email}
                    onChange={handleChange}
                    placeholder="you@example.com"
                    required
                  />
                </Form.Group>
                <Form.Group className="mb-3">
                  <Form.Label>Password</Form.Label>
                  <Form.Control
                    type="password"
                    name="password"
                    value={formData.password}
                    onChange={handleChange}
                    placeholder="••••••••"
                    required
                  />
                </Form.Group>
                <Button variant="primary" type="submit" className="w-100" disabled={loading}>
                  {loading ? 'Logging in...' : 'Login'}
                </Button>
              </Form>

              <div className="mt-4">
                <small className="text-muted d-block mb-2">Quick demo access</small>
                <Row className="g-2">
                  <Col xs={6}>
                    <Button variant="outline-primary" className="w-100" onClick={() => handleDemoLogin('user')}>
                      Demo user
                    </Button>
                  </Col>
                  <Col xs={6}>
                    <Button variant="outline-secondary" className="w-100" onClick={() => handleDemoLogin('admin')}>
                      Demo admin
                    </Button>
                  </Col>
                </Row>
              </div>
            </Card.Body>
          </Card>
        </div>
      </div>
    </Container>
  );
};

export default LoginPage;
