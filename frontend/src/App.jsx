import React from 'react';
import { Container } from 'react-bootstrap';
import AppRoutes from './routes/AppRoutes';
import NavbarTop from './components/Navbar';
import Footer from './components/Footer';
import 'bootstrap/dist/css/bootstrap.min.css';

function App() {
  return (
    <div className="app-shell">
      <NavbarTop />
      <main className="py-4">
        <Container>
          <AppRoutes />
        </Container>
      </main>
      <Footer />
    </div>
  );
}

export default App;
