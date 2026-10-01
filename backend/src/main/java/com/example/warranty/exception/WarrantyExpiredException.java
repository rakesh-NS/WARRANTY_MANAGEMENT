package com.example.warranty.exception;

public class WarrantyExpiredException extends RuntimeException {
    public WarrantyExpiredException(String message) {
        super(message);
    }
}
