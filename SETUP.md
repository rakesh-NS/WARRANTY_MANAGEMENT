# Setup Instructions

## Quick Start Guide

### Prerequisites Installation

#### Windows

1. **Java 21 Installation**
   - Download from: https://www.oracle.com/java/technologies/downloads/
   - Install and set JAVA_HOME environment variable
   - Verify: `java -version`

2. **Maven Installation**
   - Download from: https://maven.apache.org/download.cgi
   - Extract and add to PATH
   - Verify: `mvn -version`

3. **MySQL Installation**
   - Download from: https://www.mysql.com/downloads/
   - Install MySQL Server
   - Start MySQL Service

4. **Node.js Installation**
   - Download from: https://nodejs.org/
   - Install latest LTS version
   - Verify: `node -v` and `npm -v`

#### Mac

```bash
# Install Homebrew if not installed
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install Java 21
brew install openjdk@21
echo 'export PATH="/usr/local/opt/openjdk@21/bin:$PATH"' >> ~/.zshrc

# Install Maven
brew install maven

# Install MySQL
brew install mysql

# Install Node.js
brew install node
```

#### Linux (Ubuntu)

```bash
sudo apt-get update

# Install Java 21
sudo apt-get install openjdk-21-jdk

# Install Maven
sudo apt-get install maven

# Install MySQL
sudo apt-get install mysql-server

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### Database Setup

```bash
# Login to MySQL
mysql -u root -p

# Run SQL script
mysql -u root -p < warranty_db.sql

# Or manually execute:
# 1. Open warranty_db.sql
# 2. Execute entire script in MySQL client
```

### Backend Development

```bash
cd backend

# Install dependencies and build
mvn clean install

# Run application
mvn spring-boot:run

# Or package and run JAR
mvn clean package
java -jar target/product-warranty-backend-0.0.1-SNAPSHOT.jar

# Backend will be available at http://localhost:8080
```

### Frontend Development

```bash
cd frontend

# Install dependencies
npm install

# Start development server
npm run dev

# Frontend will be available at http://localhost:5173
```

## Building for Production

### Backend

```bash
cd backend

# Create production build
mvn clean package -DskipTests

# JAR file will be at: target/product-warranty-backend-0.0.1-SNAPSHOT.jar
```

### Frontend

```bash
cd frontend

# Create production build
npm run build

# Optimized files will be in dist/ folder
# Deploy dist/ folder to web server
```

## Docker Deployment

### Backend Docker

```dockerfile
# Dockerfile in backend directory
FROM eclipse-temurin:21-jdk-alpine
WORKDIR /app
COPY target/*.jar app.jar
EXPOSE 8080
ENTRYPOINT ["java","-jar","app.jar"]
```

Build and run:
```bash
docker build -t warranty-backend .
docker run -p 8080:8080 warranty-backend
```

### Docker Compose

```yaml
version: '3.8'
services:
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: warranty_db
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql
      - ./warranty_db.sql:/docker-entrypoint-initdb.d/warranty_db.sql

  backend:
    build: ./backend
    ports:
      - "8080:8080"
    environment:
      SPRING_DATASOURCE_URL: jdbc:mysql://mysql:3306/warranty_db
      SPRING_DATASOURCE_USERNAME: root
      SPRING_DATASOURCE_PASSWORD: root
    depends_on:
      - mysql

  frontend:
    build: ./frontend
    ports:
      - "3000:3000"
    depends_on:
      - backend

volumes:
  mysql_data:
```

Run with Docker Compose:
```bash
docker-compose up -d
```

## Environment Variables

### Backend (application.properties)

```properties
# Database Configuration
spring.datasource.url=jdbc:mysql://localhost:3306/warranty_db
spring.datasource.username=root
spring.datasource.password=password
spring.datasource.driver-class-name=com.mysql.cj.jdbc.Driver

# JPA/Hibernate Configuration
spring.jpa.hibernate.ddl-auto=update
spring.jpa.show-sql=false
spring.jpa.properties.hibernate.format_sql=true
spring.jpa.properties.hibernate.dialect=org.hibernate.dialect.MySQL8Dialect

# Server Configuration
server.port=8080
server.servlet.context-path=/

# Logging
logging.level.root=INFO
logging.level.com.example.warranty=DEBUG
```

### Frontend (.env)

```env
VITE_API_URL=http://localhost:8080/api
```

## Debugging

### Backend Debugging

1. **Enable Debug Mode**
   ```bash
   mvn spring-boot:run -Dspring-boot.run.arguments=--debug
   ```

2. **Use IDE Debugger**
   - Set breakpoints in your IDE
   - Run with Debug option

3. **View Logs**
   ```bash
   tail -f backend/logs/application.log
   ```

### Frontend Debugging

1. **Browser DevTools**
   - F12 or Right-click → Inspect
   - Check Console for errors
   - Use Network tab for API debugging

2. **React DevTools Extension**
   - Install React DevTools browser extension
   - Inspect component props and state

## Common Issues

### Issue: Port 8080 already in use

**Solution:**
```bash
# Windows
netstat -ano | findstr :8080
taskkill /PID <PID> /F

# Mac/Linux
lsof -i :8080
kill -9 <PID>

# Or change port in application.properties
server.port=8081
```

### Issue: MySQL connection failed

**Solution:**
```bash
# Check MySQL status
mysql -u root -p -e "SELECT 1;"

# Restart MySQL
# Windows: net start MySQL80
# Mac: brew services restart mysql
# Linux: sudo systemctl restart mysql
```

### Issue: CORS errors

**Solution:**
- Verify CorsConfig.java has correct frontend URL
- Check backend is running
- Clear browser cache

### Issue: Dependencies not downloading

**Solution:**
```bash
# Clear Maven cache
mvn clean -DremoveSnapshots

# Reinstall dependencies
mvn dependency:resolve
```

## Performance Tips

1. **Database Indexing**
   - Already configured in schema
   - Monitor slow queries

2. **Frontend Optimization**
   - Use production build
   - Enable gzip compression
   - Lazy load components

3. **Backend Optimization**
   - Use connection pooling
   - Implement caching
   - Optimize queries

## Next Steps

1. Customize the system for your needs
2. Add more validation rules
3. Implement additional features
4. Set up CI/CD pipeline
5. Deploy to production server
6. Monitor and maintain application

---

For more details, refer to README.md
