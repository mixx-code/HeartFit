# Customer Dashboard Enhancement Plan

## Overview
This plan addresses two main requirements from revisi.md:
1. **Dashboard Package Display**: Change "Pesan Sekarang" to "Detail Paket" for Reguler packages
2. **Package Detail Flow**: Show package details when clicked, then redirect to order page

## Current State Analysis

### Dashboard Structure
- Customer dashboard shows package cards for Reguler, Mingguan, Bulanan, 3 Bulanan
- Each package has "Pesan Sekarang" button that goes directly to `orders.create`
- Need to change this to show package details first, then provide order option

### Package Types
- **Reguler**: Rp 50.000 - Rp 3.540.000 (various durations)
- **Premium**: Rp 170.000 - Rp 5.830.000 (various durations)  
- **Personal**: Rp 700.000 (new package type)

## Implementation Plan

### Phase 1: Dashboard Package Display Updates

#### 1.1 Update Dashboard Layout
- **File**: `resources/views/customers/dashboard.blade.php`
- **Changes**:
  - Change "Pesan Sekarang" buttons to "Detail Paket" for Reguler packages
  - Add JavaScript to handle package detail display
  - Create modal/section to show package details when clicked
  - Maintain existing functionality for Premium and Personal packages

#### 1.2 Package Detail Display
- **Content to show**:
  - Package name and price
  - Duration options (4 hari, 8 hari, 12 hari, 24 hari, 36 hari, 72 hari)
  - Menu details (what's included)
  - Benefits/features
  - "Pesan Paket" button to proceed to order

#### 1.3 JavaScript Functionality
- **Toggle detail view**: Show/hide package details
- **Smooth transitions**: Professional UX for expanding/collapsing details
- **Mobile responsive**: Works on all screen sizes

### Phase 2: Package Detail Modal/Section

#### 2.1 Create Package Detail Component
- **Design**: Clean, informative package detail display
- **Information**: Price breakdown, duration options, menu examples
- **Call-to-action**: Clear "Pesan Paket" button
- **Styling**: Consistent with existing design system

#### 2.2 Integration Points
- **Data source**: Use existing package data from dashboard
- **Routing**: Connect to existing `orders.create` route
- **State management**: Handle selected package and duration

### Phase 3: Menu Popup Enhancement

#### 3.1 Menu Display in Date Selection
- **File**: `resources/views/customers/orders/create.blade.php`
- **Current state**: Date selection step with menu functionality
- **Enhancement**: Add food images to make menu selection more appealing
- **Implementation**: 
  - Add image gallery for menu items
  - Show food photos when customer clicks "Menu" button
  - Improve visual appeal of menu selection

#### 3.2 Image Integration
- **Image sources**: Use existing food images or add new ones
- **Display format**: Grid or carousel layout
- **Performance**: Optimize image loading and caching

## Technical Implementation Details

### Dashboard Changes
```php
// Change button text and add detail toggle
<a href="#" class="btn btn-outline-primary" onclick="togglePackageDetail('reguler')">Detail Paket</a>

// Add package detail section
<div id="package-detail-reguler" class="package-detail" style="display:none;">
  <!-- Package details content -->
</div>
```

### JavaScript Functions
```javascript
function togglePackageDetail(packageType) {
  // Toggle package detail visibility
  // Handle smooth transitions
  // Update button states
}
```

### Menu Popup Enhancement
```php
// Add images to menu display
<div class="menu-popup">
  <div class="menu-images">
    <img src="path/to/food1.jpg" alt="Menu item 1">
    <img src="path/to/food2.jpg" alt="Menu item 2">
    <!-- More images -->
  </div>
</div>
```

## Testing Requirements

### Functional Testing
- Verify "Detail Paket" buttons work correctly
- Test package detail display/hide functionality
- Ensure mobile responsiveness
- Test order flow from package details

### Visual Testing
- Check design consistency across all packages
- Verify image loading in menu popups
- Test transitions and animations

### Cross-browser Testing
- Chrome, Firefox, Safari compatibility
- Mobile browser testing
- Tablet and desktop layouts

## Success Criteria

### Dashboard
- [ ] "Pesan Sekarang" changed to "Detail Paket" for Reguler packages
- [ ] Package details display when "Detail Paket" is clicked
- [ ] Smooth transitions and professional styling
- [ ] Mobile responsive design

### Menu Enhancement
- [ ] Food images added to menu selection popup
- [ ] Improved visual appeal of menu interface
- [ ] Fast image loading and optimization

### Integration
- [ ] Seamless flow from dashboard → package details → order page
- [ ] Maintains existing functionality for other packages
- [ ] No breaking changes to current order system

## Timeline Estimate
- **Phase 1** (Dashboard updates): 2-3 hours
- **Phase 2** (Menu enhancement): 1-2 hours
- **Testing & Refinement**: 1 hour
- **Total**: 4-6 hours

## Dependencies
- Existing package data structure
- Current routing system
- Bootstrap CSS framework
- jQuery for JavaScript interactions
