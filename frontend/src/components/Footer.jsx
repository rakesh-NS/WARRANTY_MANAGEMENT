import React from 'react';
import { Container, Row, Col } from 'react-bootstrap';
import './Footer.css';

const Footer = () => (
  <footer className="bg-dark text-white text-center py-4 mt-5">
    <Container>
      <Row>
        <Col>
          <p>&copy; 2024 Product Warranty Management System. All rights reserved.</p>
        </Col>
      </Row>
    </Container>
  </footer>
);

export default Footer;
