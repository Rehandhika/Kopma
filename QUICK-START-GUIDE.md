# Quick Start Guide - New Schedule System

## 🚀 Quick Overview

The schedule system has been redesigned to support **multi-user slots** with a simplified, performance-focused approach.

## 📋 Key Concept: Multi-User Slots

**Before**: 1 slot = 1 user  
**Now**: 1 slot = 0+ users (unlimited)

```
Example:
Senin, Sesi 1:
  ✓ User A
  ✓ User B  
  ✓ User C
  [+ Tambah User]
```

## 🎯 How to Create a Schedule

### Step 1: Navigate
Go to: **Admin → Schedule → Create**

### Step 2: Set Period
- Select start date (Monday)
- Select end date (Thursday)
- Add optional notes

### Step 3: Assign Users
1. Click on any slot (day + session)
2. Select user from modal
3. Repeat to add more users to same slot
4. Click X to remove individual users

### Step 4: Review
Check the statistics panel:
- Total Assignments
- Coverage Rate
- Unique Users
- Empty Slots
- Workload Distribution

### Step 5: Save
- **Save Draft**: Save without publishing
- **Publish**: Make schedule active

## ⚡ Quick Tips

### Undo/Redo
- Use undo/redo buttons to revert changes
- History shows current position

### Conflicts
- Yellow warning: User not available
- Red error: Critical issue
- Fix before publishing

### Mobile
- Swipe through days
- Tap to assign users
- All features available

## 🔧 Configuration

Edit `config/schedule.php` to customize:
- Session times
- Workload limits
- Coverage requirements
- Performance settings

## 📊 Statistics Explained

**Total Assignments**: Number of user-slot assignments  
**Coverage Rate**: Percentage of filled slots  
**Unique Users**: Number of different users assigned  
**Empty Slots**: Slots with no users  
**Workload Distribution**: Assignments per user

## ⚠️ Important Notes

1. **No Bulk Actions**: Assign users one by one for better control
2. **No Auto-Assignment**: Manual assignment ensures accuracy
3. **No Templates**: Start fresh each time for flexibility
4. **Multi-User Friendly**: Add as many users as needed per slot

## 🐛 Troubleshooting

### Issue: Can't add user to slot
- Check if user is already in that slot
- Verify user status is "active"

### Issue: Can't publish
- Check for critical conflicts
- Ensure minimum coverage (50%)

### Issue: Statistics not updating
- Refresh page
- Clear browser cache

## 📞 Need Help?

1. Check full documentation: `SCHEDULE-PAGES-REDESIGN.md`
2. Review migration summary: `MIGRATION-SUMMARY.md`
3. Check configuration: `config/schedule.php`

---

**Version**: 2.0  
**Last Updated**: 23 November 2025
