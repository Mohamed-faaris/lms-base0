@extends('layouts.admin')

@section('page-title', 'Fun Activities')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h2 style="font-size: 24px; font-weight: 600; color: #111827;">Fun Activities Management</h2>
    <button style="background: #2563eb; color: white; font-weight: 600; padding: 10px 20px; border-radius: 6px; border: none; cursor: pointer;">
        + Add New Activity
    </button>
</div>

<!-- Filters -->
<div style="background: white; border-radius: 8px; padding: 16px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <div style="display: flex; gap: 16px;">
        <div style="flex: 1;">
            <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Course</label>
            <select style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 12px;">
                <option>All Courses</option>
                <option>Laravel Fundamentals</option>
                <option>PHP Basics</option>
            </select>
        </div>
        <div style="flex: 1;">
            <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Activity Type</label>
            <select style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 12px;">
                <option>All Types</option>
                <option>Poll</option>
                <option>Discussion</option>
                <option>Assignment</option>
                <option>Game</option>
            </select>
        </div>
    </div>
</div>

<div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f9fafb;">
            <tr>
                <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase;">ID</th>
                <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase;">Activity Title</th>
                <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase;">Course</th>
                <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase;">Type</th>
                <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase;">Points</th>
                <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase;">Status</th>
                <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase;">Actions</th>
            </tr>
        </thead>
        <tbody style="border-top: 1px solid #e5e7eb;">
            <tr>
                <td style="padding: 16px 24px; font-size: 14px; color: #111827;">1</td>
                <td style="padding: 16px 24px; font-size: 14px; font-weight: 500; color: #111827;">Weekly Code Challenge</td>
                <td style="padding: 16px 24px; font-size: 14px; color: #6b7280;">Laravel Fundamentals</td>
                <td style="padding: 16px 24px;">
                    <span style="background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 500;">Game</span>
                </td>
                <td style="padding: 16px 24px; font-size: 14px; color: #6b7280;">50</td>
                <td style="padding: 16px 24px;">
                    <span style="background: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 500;">Active</span>
                </td>
                <td style="padding: 16px 24px;">
                    <a href="#" style="color: #2563eb; margin-right: 12px;">Edit</a>
                    <a href="#" style="color: #dc2626;">Delete</a>
                </td>
            </tr>
            <tr style="border-top: 1px solid #e5e7eb;">
                <td style="padding: 16px 24px; font-size: 14px; color: #111827;">2</td>
                <td style="padding: 16px 24px; font-size: 14px; font-weight: 500; color: #111827;">Best Practices Poll</td>
                <td style="padding: 16px 24px; font-size: 14px; color: #6b7280;">PHP Basics</td>
                <td style="padding: 16px 24px;">
                    <span style="background: #dbeafe; color: #2563eb; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 500;">Poll</span>
                </td>
                <td style="padding: 16px 24px; font-size: 14px; color: #6b7280;">10</td>
                <td style="padding: 16px 24px;">
                    <span style="background: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 500;">Active</span>
                </td>
                <td style="padding: 16px 24px;">
                    <a href="#" style="color: #2563eb; margin-right: 12px;">Edit</a>
                    <a href="#" style="color: #dc2626;">Delete</a>
                </td>
            </tr>
            <tr style="border-top: 1px solid #e5e7eb;">
                <td style="padding: 16px 24px; font-size: 14px; color: #111827;">3</td>
                <td style="padding: 16px 24px; font-size: 14px; font-weight: 500; color: #111827;">Project Discussion</td>
                <td style="padding: 16px 24px; font-size: 14px; color: #6b7280;">Laravel Fundamentals</td>
                <td style="padding: 16px 24px;">
                    <span style="background: #fce7f3; color: #db2777; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 500;">Discussion</span>
                </td>
                <td style="padding: 16px 24px; font-size: 14px; color: #6b7280;">20</td>
                <td style="padding: 16px 24px;">
                    <span style="background: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 500;">Active</span>
                </td>
                <td style="padding: 16px 24px;">
                    <a href="#" style="color: #2563eb; margin-right: 12px;">Edit</a>
                    <a href="#" style="color: #dc2626;">Delete</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
