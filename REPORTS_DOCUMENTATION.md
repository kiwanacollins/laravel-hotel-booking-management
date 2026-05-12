# Hotel Management System - Reporting System Documentation

## Overview

The Hotel Management System now includes a comprehensive reporting system that provides detailed analytics and insights for Super Admins and Admins. The system generates reports for four time periods: Daily, Weekly, Monthly, and Annual.

## Features

### 1. Daily Reports
- **Real-time metrics** including total revenue, active guests, and occupancy rates
- **Hourly revenue trends** to track payment patterns throughout the day
- **Revenue by room type** analysis using doughnut charts
- **Hourly guest activity** visualization with bar charts
- **Today's transactions** detailed table with customer information
- **Current guests** list showing check-in/out dates and payment status

**Charts Included:**
- Line chart for hourly revenue trends
- Bar chart for hourly guest activity
- Doughnut chart for revenue distribution by room type

### 2. Weekly Reports
- **Weekly performance metrics** including total revenue, bookings, and average daily revenue
- **Daily revenue trends** to identify peak days
- **Revenue by room type** analysis
- **Daily guest activity** visualization
- **Comparison charts** showing revenue vs. guest count
- **Weekly transactions table** with comprehensive booking details

**Charts Included:**
- Line chart for daily revenue trends
- Bar chart for daily guest activity
- Pie chart for revenue distribution by room type
- Dual-axis comparison chart for revenue and guests

### 3. Monthly Reports
- **Comprehensive monthly metrics** including occupancy rates and bookings
- **Daily revenue and occupancy trends** with dual-axis visualization
- **Top performing rooms** ranked by bookings and revenue
- **Payment status breakdown** showing paid vs. pending payments
- **Revenue by room type** analysis
- **Monthly transactions table** with full booking details

**Charts Included:**
- Dual-axis bar/line chart for revenue and occupancy
- Doughnut chart for revenue by room type
- Pie chart for payment status distribution
- Line chart for revenue trends

**Key Insights:**
- Identifies top-performing rooms
- Tracks payment status across the month
- Shows occupancy patterns

### 4. Annual Reports
- **Year-over-year comparison** with previous year revenue
- **Monthly revenue trends** throughout the year
- **Monthly occupancy patterns** using radar charts
- **Top customers** ranked by bookings and spending
- **Guest activity trends** by month
- **Annual growth metrics** and performance indicators

**Charts Included:**
- Line chart for monthly revenue trends
- Radar chart for occupancy patterns
- Bar chart for monthly guest activity
- Line chart comparing current year vs. previous year
- Customer performance table

**Key Insights:**
- Annual growth percentage
- Best performing month
- Average daily occupancy
- Average daily revenue rate
- Top customer analysis

## Access and Permissions

### Who Can Access?
- **Super Admins** - Full access to all reports
- **Admins** - Full access to all reports
- **Customers** - No access to reporting system

### How to Access?

1. Log in as Super Admin or Admin
2. Look for the **Reports** icon in the left sidebar (chart-line icon)
3. Click the Reports icon to access the reports menu
4. Select from Daily, Weekly, Monthly, or Annual reports

### Menu Navigation

The reports system appears in the main navigation sidebar with the following structure:

```
📊 Reports (Main Menu)
├── 📅 Daily Reports
├── 📆 Weekly Reports
├── 📅 Monthly Reports
└── 📈 Annual Reports
```

## Data Displayed

### Key Metrics Across All Reports

**Revenue Metrics:**
- Total Revenue - Sum of all payments
- Average Daily/Monthly Revenue
- Revenue by Room Type
- Payment Status breakdown (Paid/Pending)

**Guest Metrics:**
- Active Guests - Current guests in hotel
- Total Bookings - Number of reservations
- Average Daily Guests
- Guest Activity trends

**Occupancy Metrics:**
- Occupancy Rate - Percentage of rooms occupied
- Average Occupancy
- Occupancy trends over time

**Room Performance:**
- Top Performing Rooms (monthly/annual)
- Bookings per room
- Revenue per room
- Occupancy rates per room

**Customer Analytics:**
- Top Customers (annual)
- Customer booking frequency
- Average customer spend
- Customer stay duration

## Charts and Visualizations

### Chart Types Used

1. **Line Charts** - For trend analysis (revenue, occupancy over time)
2. **Bar Charts** - For comparative analysis (daily/hourly data)
3. **Doughnut/Pie Charts** - For distribution analysis (revenue by room type, payment status)
4. **Radar Charts** - For multi-dimensional analysis (monthly occupancy patterns)
5. **Dual-Axis Charts** - For comparing different metrics (revenue vs. occupancy)

### Interactive Features

- All charts are interactive (hover for detailed values)
- Charts are responsive and mobile-friendly
- Color-coded for easy interpretation
- Legend support for multi-dataset charts

## Data Filtering

### Daily Reports
- **Filter by Date** - Select any past date (up to today)
- Default: Today's date

### Weekly Reports
- **Filter by Year and Week** - Select year and week number
- Default: Current year and current week

### Monthly Reports
- **Filter by Month and Year** - Dropdown for month and input for year
- Default: Current month and year

### Annual Reports
- **Filter by Year** - Select any year
- Default: Current year

## Technical Implementation

### Files Created/Modified

**New Files:**
- `/app/Http/Controllers/ReportController.php` - Main controller for report generation
- `/resources/views/report/index.blade.php` - Reports menu page
- `/resources/views/report/daily.blade.php` - Daily report view
- `/resources/views/report/weekly.blade.php` - Weekly report view
- `/resources/views/report/monthly.blade.php` - Monthly report view
- `/resources/views/report/annual.blade.php` - Annual report view

**Modified Files:**
- `/routes/web.php` - Added report routes
- `/resources/views/template/include/_sidebar.blade.php` - Added Reports menu icon

### Routes

```php
Route::group(['middleware' => ['auth', 'checkRole:Super,Admin']], function () {
    Route::name('report.')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('index');
        Route::get('/reports/daily', [ReportController::class, 'daily'])->name('daily');
        Route::get('/reports/weekly', [ReportController::class, 'weekly'])->name('weekly');
        Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/reports/annual', [ReportController::class, 'annual'])->name('annual');
    });
});
```

### Database Queries

The reporting system uses the following models and relationships:

- **Transaction** - For reservation data (check_in, check_out, room, customer)
- **Payment** - For revenue data (price, status, date)
- **Room** - For room information (number, price, type)
- **Customer** - For customer information
- **Type** - For room type information
- **User** - For customer/staff information

### Performance Considerations

- **Caching:** Consider implementing caching for historical reports
- **Aggregation:** Reports aggregate data on-the-fly; for large datasets, consider pre-aggregating data
- **Indexing:** Ensure database tables have proper indexes on `created_at`, `check_in`, `check_out` columns

## Quick Start Guide

### Step 1: Login
1. Access your hotel management system
2. Login as Super Admin or Admin

### Step 2: Navigate to Reports
1. Click the Reports icon (📊) in the left sidebar
2. You'll see the Reports Dashboard with quick stats and links to all report types

### Step 3: Generate Report
1. Click on the report type you want (Daily, Weekly, Monthly, or Annual)
2. Optionally adjust filters (date, month, year)
3. View charts and data automatically loaded

### Step 4: Analyze Data
1. Hover over charts to see detailed values
2. Scroll through tables for detailed transactions
3. Compare metrics across different time periods

## Features Summary

| Feature | Daily | Weekly | Monthly | Annual |
|---------|:-----:|:------:|:-------:|:------:|
| Revenue Metrics | ✓ | ✓ | ✓ | ✓ |
| Guest Metrics | ✓ | ✓ | ✓ | ✓ |
| Occupancy Analysis | ✓ | ✓ | ✓ | ✓ |
| Hourly Breakdown | ✓ | - | - | - |
| Top Performers | - | - | ✓ | ✓ |
| Year Comparison | - | - | - | ✓ |
| Transaction Tables | ✓ | ✓ | ✓ | ✓ |
| Multiple Charts | ✓ | ✓ | ✓ | ✓ |

## Future Enhancements

Potential features for future versions:

1. **PDF Export** - Export reports as PDF documents
2. **Excel Export** - Export data to Excel spreadsheets
3. **Custom Date Ranges** - Select any date range for reporting
4. **Email Reports** - Schedule and email reports
5. **Real-time Dashboards** - Live updating charts
6. **Custom Metrics** - Create custom KPIs
7. **Predictive Analytics** - Forecast future trends
8. **Room Type Analysis** - Deep dive into room type performance
9. **Staff Performance** - Analyze staff performance metrics
10. **Comparison Reports** - Compare different time periods

## Troubleshooting

### Charts Not Displaying
- Ensure Chart.js library is properly loaded (check CDN)
- Check browser console for JavaScript errors
- Clear browser cache and refresh

### No Data Shown
- Verify dates/year are correct and have data
- Check that you're logged in as Super Admin or Admin
- Ensure data exists in the database

### Slow Performance
- Reports with large date ranges may take longer to load
- Consider limiting date range or pre-filtering data
- Check database indexes on timestamp columns

## Support and Maintenance

For issues or feature requests:
1. Check the troubleshooting section above
2. Review application logs in `/storage/logs/`
3. Contact system administrator

---

**Version:** 1.0
**Last Updated:** 2024
**Compatible With:** Laravel 10+, Chart.js 3.9+
