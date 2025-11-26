# Alpine.js Interactions Guide

## Quick Reference for Edit Schedule Interface

### 1. Tooltips

**How to Use:**
```html
<button x-data x-tooltip="'Your helpful text here'">
    Hover me
</button>
```

**Features:**
- Appears after 300ms hover
- Smart positioning (auto-adjusts at screen edges)
- Smooth fade animations
- Works on focus for accessibility
- Auto-hides on click

**Where Used:**
- Save/Cancel buttons in header
- Add/Clear buttons in slot cards
- Remove user buttons
- All interactive elements

---

### 2. Loading States

**Automatic (Livewire):**
Loading automatically shows when any Livewire action is triggered.

**Manual Control:**
```javascript
// Show loading
Alpine.store('loading').show('Processing your request...');

// Hide loading
Alpine.store('loading').hide();

// Change message
Alpine.store('loading').setMessage('Almost done...');
```

**Visual Elements:**
- Triple-ring animated spinner
- Progress bar (0-100%)
- Bouncing dots
- Custom message
- Backdrop blur

---

### 3. Toast Notifications

**Usage:**
```javascript
// Success (green)
Alpine.store('toast').success('Changes saved successfully!');

// Error (red)
Alpine.store('toast').error('Failed to save changes', 4000);

// Warning (yellow)
Alpine.store('toast').warning('Please review your changes');

// Info (blue)
Alpine.store('toast').info('New feature available');
```

**Features:**
- Auto-dismiss (default 3 seconds)
- Slide-in from right
- Color-coded by type
- Manual close button
- Bounce-in animation

**From Livewire:**
```php
$this->dispatch('notify', [
    'message' => 'User added successfully!',
    'type' => 'success',
    'duration' => 3000
]);
```

---

### 4. Confirmation Dialogs

**Usage:**
```javascript
Alpine.store('confirm').ask({
    title: 'Delete User?',
    message: 'This will remove the user from the schedule.',
    confirmText: 'Yes, Delete',
    cancelText: 'Cancel',
    confirmClass: 'bg-red-600 hover:bg-red-700',
    onConfirm: () => {
        // Execute delete action
        $wire.removeUser(userId);
    },
    onCancel: () => {
        // Optional: Handle cancellation
        console.log('User cancelled');
    }
});
```

**Features:**
- Modal overlay with backdrop blur
- Customizable buttons
- Keyboard support (ESC to close)
- Smooth animations
- Callback support

**Built-in Confirmations:**
- Remove user from slot
- Clear entire slot
- Discard changes (Ctrl+Z)

---

### 5. Keyboard Shortcuts

| Shortcut | Action | Description |
|----------|--------|-------------|
| **ESC** | Close | Closes any open modal or dialog |
| **Ctrl/Cmd + S** | Save | Saves all changes to database |
| **Ctrl/Cmd + Z** | Discard | Discards unsaved changes (with confirmation) |

**Features:**
- Prevents default browser behavior
- Shows confirmation for destructive actions
- Provides visual feedback
- Checks button state before executing

---

### 6. Button Animations

**Hover Effects:**
- Scale up (105%)
- Icon rotation/scale
- Shadow enhancement
- Ripple backgrounds

**Click Effects:**
- Scale down (95%)
- Quick bounce back
- Visual feedback

**Examples:**
```html
<!-- Save button with icon animation -->
<button class="group ...">
    <svg class="group-hover:scale-110 group-hover:rotate-12">
        <!-- Icon -->
    </svg>
    <span>Save</span>
</button>

<!-- Cancel button with rotation -->
<button class="group ...">
    <svg class="group-hover:rotate-180">
        <!-- Icon -->
    </svg>
    <span>Cancel</span>
</button>
```

---

### 7. Slot Card Interactions

**Hover State:**
- Card scales up slightly
- Shows blue ring
- Action buttons become more visible
- Smooth transitions

**Expand/Collapse:**
```html
<button @click="expanded = !expanded">
    <span x-show="!expanded">Show more</span>
    <span x-show="expanded">Show less</span>
</button>

<div x-show="expanded" x-transition>
    <!-- Additional content -->
</div>
```

---

### 8. User Badge Interactions

**Hover Effects:**
- Border color changes to blue
- Background lightens
- Remove button appears
- Scale up slightly

**Remove Action:**
```html
<button @click="$dispatch('confirm-remove', { 
    assignmentId: 123, 
    userName: 'John Doe' 
})">
    Remove
</button>
```

---

### 9. Modal Animations

**Open Animation:**
1. Backdrop fades in (300ms)
2. Modal scales up from 90% to 100%
3. Modal fades in
4. Bounce effect on entry

**Close Animation:**
1. Modal scales down to 90%
2. Modal fades out
3. Backdrop fades out
4. Smooth exit (200ms)

**Trigger Close:**
- Click backdrop
- Press ESC
- Click close button
- Click cancel button

---

### 10. Custom Animations

**Available CSS Classes:**

| Class | Effect | Duration |
|-------|--------|----------|
| `animate-pulse-slow` | Slow pulsing | 3s |
| `animate-fade-in-up` | Fade in from bottom | 0.3s |
| `animate-slide-in-right` | Slide from right | 0.3s |
| `animate-bounce-in` | Bounce entrance | 0.5s |
| `animate-shake` | Shake effect | 0.5s |
| `animate-glow` | Glowing effect | 2s |
| `animate-spin-slow` | Slow rotation | 2s |
| `animate-gradient` | Gradient shift | 3s |

**Usage:**
```html
<div class="animate-bounce-in">
    Content appears with bounce
</div>
```

---

## Best Practices

### 1. Tooltips
- Keep text concise (under 50 characters)
- Use for clarification, not essential info
- Ensure functionality works without tooltips

### 2. Loading States
- Use for operations > 200ms
- Provide meaningful messages
- Don't block UI unnecessarily

### 3. Toast Notifications
- Use success for completed actions
- Use error for failures
- Use warning for cautions
- Use info for neutral updates

### 4. Confirmation Dialogs
- Use for destructive actions
- Make consequences clear
- Provide easy cancel option
- Use appropriate button colors

### 5. Animations
- Keep animations subtle (200-300ms)
- Don't overuse animations
- Ensure animations enhance UX
- Test on slower devices

---

## Troubleshooting

### Tooltip Not Showing
- Ensure element has `x-data` directive
- Check tooltip text is not empty
- Verify Alpine.js is loaded

### Loading State Stuck
- Check Livewire request completed
- Manually call `Alpine.store('loading').hide()`
- Check browser console for errors

### Toast Not Appearing
- Verify Alpine.js store is initialized
- Check z-index conflicts
- Ensure message is not empty

### Keyboard Shortcuts Not Working
- Check if input field is focused
- Verify event listener is attached
- Check browser console for errors

---

## Examples in Context

### Complete User Addition Flow
```javascript
// 1. User clicks "Add User" button
// → Tooltip shows on hover
// → Button scales on click

// 2. Modal opens
// → Backdrop fades in
// → Modal scales up with bounce

// 3. User selects user
// → Checkbox animates
// → Selection count updates

// 4. User clicks "Add"
// → Loading state shows
// → Livewire processes request

// 5. Success
// → Loading hides
// → Toast notification appears
// → Modal closes
// → Slot card updates with animation

// 6. If error
// → Loading hides
// → Error toast appears
// → User can retry
```

### Complete Slot Clear Flow
```javascript
// 1. User clicks "Clear Slot" button
// → Tooltip shows on hover
// → Button scales on click

// 2. Confirmation dialog appears
// → Backdrop fades in
// → Dialog scales up

// 3. User confirms
// → Dialog closes
// → Loading state shows
// → Livewire processes request

// 4. Success
// → Loading hides
// → Success toast appears
// → Slot card updates (shows empty state)
// → Smooth transition
```

---

## Performance Tips

1. **Tooltip Delay**: 300ms prevents flash on quick hovers
2. **Loading Delay**: 200ms prevents flash on quick operations
3. **CSS Transforms**: Use transform instead of position for animations
4. **Debouncing**: Search inputs use 300ms debounce
5. **Lazy Loading**: Modals load content on demand

---

## Accessibility

1. **Keyboard Navigation**: All actions accessible via keyboard
2. **Focus Management**: Focus trapped in modals
3. **Screen Readers**: ARIA labels on interactive elements
4. **Focus Visible**: Clear focus indicators (2px blue outline)
5. **Color Contrast**: WCAG AA compliant colors

---

## Browser Support

| Browser | Version | Support |
|---------|---------|---------|
| Chrome | 90+ | ✅ Full |
| Firefox | 88+ | ✅ Full |
| Safari | 14+ | ✅ Full |
| Edge | 90+ | ✅ Full |
| Mobile Safari | 14+ | ✅ Full |
| Mobile Chrome | 90+ | ✅ Full |

---

## Summary

The Alpine.js interactions provide:
- ✅ Intuitive user feedback
- ✅ Smooth, professional animations
- ✅ Accessible keyboard navigation
- ✅ Error prevention with confirmations
- ✅ Clear loading states
- ✅ Helpful tooltips
- ✅ Responsive design
- ✅ Cross-browser compatibility

All interactions are designed to enhance the user experience while maintaining performance and accessibility standards.
