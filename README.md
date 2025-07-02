# Cinema Booking Backend

A simple PHP API for managing a cinema booking system. Handles movies, showtimes, bookings, and user management.

## What it does

- Manage movies and their posters
- Schedule movie showtimes
- Handle ticket bookings
- Manage users (admin and regular)
- Serve images securely

## Quick Start

1. **Set up the database**

   - Create a MySQL database called `cinema_booking_db`
   - Update the database settings in `connection/db_connection.php`

2. **Run the setup**

   ```bash
   php migrate.php
   php seeders/seed.php  # Optional - adds sample data
   ```

3. **Point your web server** to this folder and make sure the `uploads/` folder is writable

## API Endpoints

### Movies

- `GET /api/movies.php` - Get all movies
- `GET /api/movies.php?id=1` - Get a specific movie
- `POST /api/movies.php` - Add a new movie (admin only)
- `PUT /api/movies.php?id=1` - Update a movie (admin only)
- `DELETE /api/movies.php?id=1` - Delete a movie (admin only)

### Showtimes

- `GET /api/showtimes.php` - Get all showtimes
- `GET /api/showtimes.php?movie_id=1` - Get showtimes for a movie
- `POST /api/showtimes.php` - Add a showtime (admin only)

### Bookings

- `GET /api/bookings.php` - Get all bookings
- `POST /api/bookings.php` - Make a booking
- `DELETE /api/bookings.php?id=1` - Cancel a booking

### Users

- `GET /api/users.php` - Get all users (admin only)
- `POST /api/users.php` - Register a new user
- `PUT /api/users.php?id=1` - Update user info

### Images

- `GET /api/images.php?path=posters/movie.jpg` - Get uploaded images

## File Uploads

Movie posters can be uploaded when creating movies:

- Supported: JPG, PNG, GIF
- Max size: 5MB
- Files are automatically renamed to prevent conflicts

## Testing

You can test the API with tools like Postman

**Note**: This is just the backend API. You'll need a [frontend](https://github.com/MarkSemaan/cinema-booking-frontend/) to interact with it.
