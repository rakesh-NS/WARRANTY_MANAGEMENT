package com.example.warranty.repository;

import java.util.Optional;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import com.example.warranty.entity.UserEntity;

@Repository
public interface UserRepository extends JpaRepository<UserEntity, Long> {
    Optional<UserEntity> findByEmailIgnoreCase(String email);
    boolean existsByEmailIgnoreCase(String email);

    default Optional<UserEntity> findByEmail(String email) {
        return findByEmailIgnoreCase(email == null ? null : email.trim());
    }

    default boolean existsByEmail(String email) {
        return existsByEmailIgnoreCase(email == null ? null : email.trim());
    }
}
