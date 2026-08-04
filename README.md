# Product Warranty Management System

A complete full-stack web application for managing product warranties and claims. Built with **React**, **Spring Boot**, and **MySQL** following industry best practices and clean architecture principles.

## 🎯 Features

### User Module
- User Registration with email validation
- User Login with password hashing
- View and Update User Profile
- Role-based access (USER/ADMIN)

### Product Module
- Register new products
- Update product details
- Delete products
- View all products
- Search products by name, brand, or model
- Store product details (name, brand, model, serial number, purchase date, price)

### Warranty Module
- Create warranty for products
- Track warranty start and expiry dates
- Monitor warranty status
- View warranty details
- Extend warranty (Admin only)

### Warranty Claim Module
- Submit warranty claims
- Upload issue descriptions
- Track claim status
- View claim history
- Admin approval/rejection of claims
- Add admin remarks to claims

### Admin Module
- Admin Login
- Dashboard with statistics:
  - Total Users
  - Total Products
  - Active Warranties
  - Expired Warranties
  - Pending Claims
  - Approved Claims
  - Rejected Claims
- Manage users
- Manage products
- Manage warranty claims

## 🛠️ Tech Stack

### Frontend
- **React 18** - UI library
- **React Router DOM** - Client-side routing
- **Axios** - HTTP client
- **Bootstrap 5** - CSS framework
- **Vite** - Build tool

### Backend
- **Java 21** - Programming language
- **Spring Boot 3** - Framework
- **Spring MVC** - Web framework
- **Spring Data JPA** - ORM
- **Hibernate** - JPA implementation
- **Maven** - Build tool
- **Lombok** - Code generation
- **Jakarta Validation** - Input validation

### Database
- **MySQL 8+** - Relational database

## 📁 Project Structure

```
product-warranty/
├── backend/
│   ├── src/
│   │   └── main/
│   │       ├── java/com/example/warranty/
│   │       │   ├── WarrantyApplication.java
│   │       │   ├── config/
│   │       │   │   └── CorsConfig.java
│   │       │   ├── controller/
│   │       │   │   ├── AdminController.java
│   │       │   │   ├── UserController.java
│   │       │   │   ├── ProductController.java
│   │       │   │   ├── WarrantyController.java
│   │       │   │   └── ClaimController.java
│   │       │   ├── service/
│   │       │   │   ├── UserService.java
│   │       │   │   ├── ProductService.java
│   │       │   │   ├── WarrantyService.java
│   │       │   │   ├── ClaimService.java
│   │       │   │   └── impl/
│   │       │   │       ├── UserServiceImpl.java
│   │       │   │       ├── ProductServiceImpl.java
│   │       │   │       ├── WarrantyServiceImpl.java
│   │       │   │       └── ClaimServiceImpl.java
│   │       │   ├── repository/
│   │       │   │   ├── UserRepository.java
│   │       │   │   ├── ProductRepository.java
│   │       │   │   ├── WarrantyRepository.java
│   │       │   │   └── ClaimRepository.java
│   │       │   ├── entity/
│   │       │   │   ├── UserEntity.java
│   │       │   │   ├── ProductEntity.java
│   │       │   │   ├── WarrantyEntity.java
│   │       │   │   └── ClaimEntity.java
│   │       │   ├── dto/
│   │       │   │   ├── UserRequestDto.java
│   │       │   │   ├── UserResponseDto.java
│   │       │   │   ├── LoginRequestDto.java
│   │       │   │   ├── LoginResponseDto.java
│   │       │   │   ├── ProductRequestDto.java
│   │       │   │   ├── ProductResponseDto.java
│   │       │   │   ├── WarrantyRequestDto.java
│   │       │   │   ├── WarrantyResponseDto.java
│   │       │   │   ├── ClaimRequestDto.java
│   │       │   │   └── ClaimResponseDto.java
│   │       │   ├── exception/
│   │       │   │   ├── ApiError.java
│   │       │   │   ├── GlobalExceptionHandler.java
│   │       │   │   ├── ResourceNotFoundException.java
│   │       │   │   ├── DuplicateResourceException.java
│   │       │   │   └── InvalidLoginException.java
│   │       │   └── security/
│   │       └── resources/
│   │           └── application.properties
│   └── pom.xml
├── frontend/
│   ├── src/
│   │   ├── components/
│   │   │   ├── Navbar.jsx
│   │   │   ├── Navbar.css
│   │   │   ├── Footer.jsx
│   │   │   └── Footer.css
│   │   ├── pages/
│   │   │   ├── HomePage.jsx
│   │   │   ├── LoginPage.jsx
│   │   │   ├── RegisterPage.jsx
│   │   │   ├── DashboardPage.jsx
│   │   │   ├── ProductListPage.jsx
│   │   │   ├── AddProductPage.jsx
│   │   │   ├── EditProductPage.jsx
│   │   │   ├── WarrantyDetailsPage.jsx
│   │   │   ├── ClaimHistoryPage.jsx
│   │   │   ├── AdminDashboardPage.jsx
│   │   │   ├── ManageUsersPage.jsx
│   │   │   ├── ManageProductsPage.jsx
│   │   │   ├── ManageClaimsPage.jsx
│   │   │   └── ProfilePage.jsx
│   │   ├── routes/
│   │   │   └── AppRoutes.jsx
│   │   ├── services/
│   │   │   ├── api.js
│   │   │   └── serviceApi.js
│   │   ├── App.jsx
│   │   ├── App.css
│   │   ├── index.css
│   │   └── main.jsx
│   ├── index.html
│   ├── package.json
│   ├── vite.config.js
│   └── .env (for configuration)
├── warranty_db.sql
└── README.md
```

## 🚀 Getting Started

### Prerequisites
- **Java 21** or higher
- **Node.js 18+** and **npm**
- **MySQL 8+**
- **Maven 3.8+**

### Backend Setup

#### 1. Database Setup

```bash
# Login to MySQL
mysql -u root -p

# Execute the SQL script
source warranty_db.sql

# Or manually:
CREATE DATABASE warranty_db;
USE warranty_db;
-- (then run the schema from warranty_db.sql)
```

#### 2. Configure Database Connection

Edit `backend/src/main/resources/application.properties`:

```properties
spring.datasource.url=jdbc:mysql://localhost:3306/warranty_db?useSSL=false&allowPublicKeyRetrieval=true&serverTimezone=UTC
spring.datasource.username=root
spring.datasource.password=your_mysql_password
spring.jpa.hibernate.ddl-auto=update
```

#### 3. Build and Run Backend

```bash
cd backend

# Build the project
mvn clean install

# Run the application
mvn spring-boot:run
```

The backend server will start on `http://localhost:8080`

### Frontend Setup

#### 1. Install Dependencies

```bash
cd frontend

npm install
```

#### 2. Configure API Endpoint

Edit `frontend/src/services/api.js` if backend URL is different:

```javascript
const API_BASE_URL = 'http://localhost:8080/api';
```

#### 3. Run Development Server

```bash
npm run dev
```

The frontend will start on `http://localhost:5173`

## 📝 API Documentation

### Authentication Endpoints

#### Register User
```
POST /api/users/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "phone": "1234567890",
  "role": "USER"
}
```

#### Login
```
POST /api/users/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

Response:
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "role": "USER",
  "token": "token-placeholder"
}
```

### User Endpoints

#### Get Profile
```
GET /api/users/{id}
```

#### Update Profile
```
PUT /api/users/{id}
Content-Type: application/json

{
  "name": "John Doe Updated",
  "email": "john@example.com",
  "password": "newpassword",
  "phone": "9876543210",
  "role": "USER"
}
```

### Product Endpoints

#### Get All Products
```
GET /api/products
```

#### Get Product by ID
```
GET /api/products/{id}
```

#### Create Product
```
POST /api/products
Content-Type: application/json

{
  "productName": "Laptop",
  "brand": "Dell",
  "model": "XPS 13",
  "serialNumber": "SN123456",
  "purchaseDate": "2023-01-15",
  "price": 999.99,
  "userId": 1
}
```

#### Update Product
```
PUT /api/products/{id}
Content-Type: application/json

{
  "productName": "Laptop Updated",
  "brand": "Dell",
  "model": "XPS 13",
  "serialNumber": "SN123456",
  "purchaseDate": "2023-01-15",
  "price": 1099.99,
  "userId": 1
}
```

#### Delete Product
```
DELETE /api/products/{id}
```

#### Search Products
```
GET /api/products/search?q=laptop
```

### Warranty Endpoints

#### Get All Warranties
```
GET /api/warranty
```

#### Create Warranty
```
POST /api/warranty
Content-Type: application/json

{
  "productId": 1,
  "userId": 1,
  "startDate": "2023-01-15",
  "expiryDate": "2025-01-15",
  "status": "Active"
}
```

#### Update Warranty
```
PUT /api/warranty/{id}
Content-Type: application/json

{
  "productId": 1,
  "userId": 1,
  "startDate": "2023-01-15",
  "expiryDate": "2026-01-15",
  "status": "Active"
}
```

### Claim Endpoints

#### Submit Claim
```
POST /api/claims
Content-Type: application/json

{
  "warrantyId": 1,
  "claimDate": "2024-01-10",
  "issueDescription": "Device not turning on"
}
```

Response:
```json
{
  "id": 1,
  "warrantyId": 1,
  "claimDate": "2024-01-10",
  "issueDescription": "Device not turning on",
  "status": "Pending",
  "adminRemarks": null,
  "productId": 1
}
```

#### Get All Claims
```
GET /api/claims
```

#### Update Claim Status
```
PUT /api/claims/{id}?status=Approved&adminRemarks=Approved+for+replacement
```

### Admin Endpoints

#### Admin Login
```
POST /api/admin/login
Content-Type: application/json

{
  "email": "admin@warranty.com",
  "password": "adminpass"
}
```

#### Get Dashboard Statistics
```
GET /api/admin/dashboard
```

Response:
```json
{
  "totalUsers": 10,
  "totalProducts": 25,
  "activeWarranties": 20,
  "expiredWarranties": 5,
  "pendingClaims": 3,
  "approvedClaims": 15,
  "rejectedClaims": 2
}
```

## 🔒 Security Features

- **Password Hashing**: BCrypt encryption for passwords
- **CORS Configuration**: Restricted to frontend domain
- **Input Validation**: Jakarta Validation annotations
- **Error Handling**: Global exception handler
- **Role-Based Access**: USER and ADMIN roles
- **Email Uniqueness**: Duplicate email prevention

## 🗄️ Database Schema

### Users Table
- id (Primary Key)
- name
- email (Unique)
- password
- phone
- role
- created_at
- updated_at

### Products Table
- id (Primary Key)
- product_name
- brand
- model
- serial_number (Unique)
- purchase_date
- price
- user_id (Foreign Key → users.id)
- created_at
- updated_at

### Warranty Table
- id (Primary Key)
- product_id (Foreign Key → products.id, Unique)
- user_id (Foreign Key → users.id)
- start_date
- expiry_date
- status
- created_at
- updated_at

### Claims Table
- id (Primary Key)
- warranty_id (Foreign Key → warranty.id)
- claim_date
- issue_description
- status
- admin_remarks
- created_at
- updated_at

## 📊 Relationships

```
User (1) ──── (Many) Product
User (1) ──── (Many) Warranty
Product (1) ──── (1) Warranty
Warranty (1) ──── (Many) Claim
```

## 🧪 Testing Sample Accounts

### Admin Account
- **Email**: admin@warranty.com
- **Password**: password (hashed in DB)
- **Role**: ADMIN

### User Accounts
- **Email**: john@warranty.com / **Password**: password
- **Email**: jane@warranty.com / **Password**: password

> Note: Update these credentials in production!

## 🌐 Deployment

### Backend Deployment (Docker)

Create a `Dockerfile` in the backend directory:

```dockerfile
FROM eclipse-temurin:21-jdk-alpine
WORKDIR /app
COPY target/product-warranty-backend-0.0.1-SNAPSHOT.jar app.jar
EXPOSE 8080
ENTRYPOINT ["java","-jar","app.jar"]
```

Build and run:
```bash
docker build -t warranty-backend .
docker run -p 8080:8080 warranty-backend
```

### Frontend Deployment (Vercel/Netlify)

```bash
cd frontend
npm run build
```

Deploy the `dist` folder to Vercel or Netlify.

## 🐛 Troubleshooting

### Backend Issues

**Port 8080 already in use**
```bash
# Change port in application.properties
server.port=8081
```

**Database connection failed**
- Ensure MySQL is running
- Check database credentials
- Verify database exists: `SHOW DATABASES;`

### Frontend Issues

**CORS errors**
- Check `CorsConfig.java` allows frontend URL
- Ensure backend is running on correct port

**API calls failing**
- Verify backend URL in `api.js`
- Check browser console for detailed errors

## 📈 Performance Optimization

- Database indexing on frequently searched columns
- Lazy loading for large datasets
- Pagination support (can be added)
- Caching strategies (can be implemented)

## 📚 Code Standards

- **Clean Architecture**: Layered architecture (controller → service → repository)
- **SOLID Principles**: Applied throughout
- **DRY (Don't Repeat Yourself)**: Reusable components and utilities
- **Naming Conventions**: Clear, descriptive names
- **Documentation**: Commented code for complex logic
- **Error Handling**: Comprehensive exception handling

## 🔄 Git Workflow

```bash
# Clone repository
git clone <repository-url>
cd product-warranty

# Create feature branch
git checkout -b feature/feature-name

# Commit changes
git commit -m "Add feature-name"

# Push changes
git push origin feature/feature-name

# Create Pull Request
```

## 📝 Logging

Configure logging in `application.properties`:

```properties
logging.level.root=INFO
logging.level.com.example.warranty=DEBUG
logging.pattern.console=%d{HH:mm:ss.SSS} [%thread] %-5level %logger{36} - %msg%n
```

## 🚀 Future Enhancements

- [ ] JWT Token Authentication
- [ ] Email Notifications
- [ ] File Upload for Issue Descriptions
- [ ] Warranty Extension Timeline
- [ ] Advanced Analytics Dashboard
- [ ] Mobile Application
- [ ] Payment Integration
- [ ] Report Generation (PDF/Excel)
- [ ] SMS Notifications
- [ ] Two-Factor Authentication

## 📄 License

This project is licensed under the MIT License.

## 👥 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📞 Support

For issues and questions, please create an issue in the repository or contact the development team.

---

**Built with ❤️ by Senior Full Stack Java Developer**

Version: 1.0.0  
Last Updated: 2024
