#!/bin/bash

# Multi-Tenant Flat & Bill Management System - Test Runner
# This script runs all tests with proper configuration

echo "🏠 Multi-Tenant Flat & Bill Management System - Test Suite"
echo "=================================================="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Please run this script from the Laravel project root directory"
    exit 1
fi

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "❌ Error: PHP is not installed or not in PATH"
    exit 1
fi

# Check if Composer is available
if ! command -v composer &> /dev/null; then
    echo "❌ Error: Composer is not installed or not in PATH"
    exit 1
fi

echo "📋 Running Test Suite..."
echo ""

# Run unit tests
echo "🧪 Running Unit Tests..."
echo "----------------------"
php artisan test tests/Unit --verbose

if [ $? -eq 0 ]; then
    echo "✅ Unit tests passed!"
else
    echo "❌ Unit tests failed!"
    exit 1
fi

echo ""

# Run feature tests
echo "🎯 Running Feature Tests..."
echo "-------------------------"
php artisan test tests/Feature --verbose

if [ $? -eq 0 ]; then
    echo "✅ Feature tests passed!"
else
    echo "❌ Feature tests failed!"
    exit 1
fi

echo ""

# Run integration tests
echo "🔗 Running Integration Tests..."
echo "------------------------------"
php artisan test tests/Integration --verbose

if [ $? -eq 0 ]; then
    echo "✅ Integration tests passed!"
else
    echo "❌ Integration tests failed!"
    exit 1
fi

echo ""

# Run all tests with coverage (if available)
echo "📊 Running All Tests with Coverage..."
echo "------------------------------------"
php artisan test --coverage --min=80

if [ $? -eq 0 ]; then
    echo "✅ All tests passed with coverage!"
else
    echo "❌ Tests failed or coverage below threshold!"
    exit 1
fi

echo ""
echo "🎉 All tests completed successfully!"
echo "=================================================="
echo "Test Summary:"
echo "- Unit Tests: ✅ Passed"
echo "- Feature Tests: ✅ Passed"
echo "- Integration Tests: ✅ Passed"
echo "- Coverage: ✅ Above 80%"
echo ""
echo "🚀 The Multi-Tenant Flat & Bill Management System is ready for production!"



