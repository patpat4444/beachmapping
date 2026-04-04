<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Beach Details — Dagat Ta bAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="/landing.css">
    <script>
      (function() {
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
      })();
    </script>
    <style>
      * { margin: 0; padding: 0; box-sizing: border-box; }
      
      body {
        background: var(--page-bg, #0a0f1a);
        color: var(--page-text, #e6f7ff);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        min-height: 100vh;
      }
      
      [data-theme="light"] body {
        background: #f5f5f0;
        color: #1a1a1a;
      }
      
      /* Top Navigation */
      .top-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 2rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        background: var(--page-bg, #0a0f1a);
      }
      
      [data-theme="light"] .top-nav {
        border-bottom-color: rgba(0,0,0,0.1);
        background: #fff;
      }
      
      .nav-left {
        display: flex;
        align-items: center;
        gap: 3rem;
      }
      
      .logo {
        font-size: 1.5rem;
        font-weight: 600;
        color: #7ecce0;
      }
      
      .logo span {
        font-style: italic;
      }
      
      .nav-links {
        display: flex;
        gap: 2rem;
      }
      
      .nav-links a {
        color: #7ecce0;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
      }
      
      .nav-links a:hover {
        color: #fff;
      }
      
      [data-theme="light"] .nav-links a {
        color: #1a7a8f;
      }
      
      .nav-right {
        display: flex;
        align-items: center;
        gap: 1rem;
      }
      
      .theme-toggle {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255,255,255,0.1);
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        cursor: pointer;
      }
      
      .theme-toggle i {
        color: #fbbf24;
      }
      
      .back-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: transparent;
        border: 1px solid rgba(126, 204, 224, 0.3);
        border-radius: 8px;
        color: #7ecce0;
        text-decoration: none;
        font-size: 0.875rem;
      }
      
      [data-theme="light"] .back-btn {
        border-color: #2a9db8;
        color: #1a7a8f;
      }
      
      /* Hero Section */
      .hero-section {
        position: relative;
        height: 450px;
        background: linear-gradient(180deg, rgba(10,15,26,0) 0%, rgba(10,15,26,0.9) 100%),
                    url('/images/beach-bg.jpg') center/cover, #1a3a4a;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 2rem;
      }
      
      .hero-content {
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
      }
      
      .hero-left {
        flex: 1;
      }
      
      .hero-tags {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1rem;
      }
      
      .tag {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        background: rgba(126, 204, 224, 0.2);
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        color: #7ecce0;
      }
      
      .tag.open {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
      }
      
      .beach-title {
        font-size: 3rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #fff;
      }
      
      .beach-title span {
        font-style: italic;
        color: #7ecce0;
      }
      
      .hero-rating {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.75rem;
      }
      
      .stars {
        color: #fbbf24;
        font-size: 1rem;
      }
      
      .rating-text {
        color: #fff;
        font-size: 1rem;
      }
      
      .rating-text span {
        color: rgba(255,255,255,0.6);
      }
      
      .hero-location {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #7ecce0;
        font-size: 1rem;
      }
      
      .hero-right {
        display: flex;
        align-items: center;
        gap: 1rem;
      }
      
      .photo-gallery {
        display: flex;
        gap: 0.5rem;
      }
      
      .photo-thumb {
        width: 80px;
        height: 60px;
        border-radius: 8px;
        background: rgba(126, 204, 224, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        border: 2px solid rgba(126, 204, 224, 0.3);
      }
      
      .photo-more {
        width: 80px;
        height: 60px;
        border-radius: 8px;
        background: rgba(126, 204, 224, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        color: #7ecce0;
        font-weight: 500;
      }
      
      /* Action Bar */
      .action-bar {
        display: flex;
        gap: 1rem;
        padding: 1.5rem 2rem;
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
      }
      
      .btn-save {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 2rem;
        background: #7ecce0;
        color: #0a0f1a;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
      }
      
      .btn-share {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 2rem;
        background: rgba(126, 204, 224, 0.1);
        color: #7ecce0;
        border: 1px solid rgba(126, 204, 224, 0.3);
        border-radius: 12px;
        font-size: 1rem;
        cursor: pointer;
      }
      
      /* Main Content Grid */
      .main-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem 2rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
      }
      
      /* Cards */
      .card {
        background: rgba(21, 34, 53, 0.6);
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid rgba(126, 204, 224, 0.1);
        margin-bottom: 1.5rem;
      }
      
      [data-theme="light"] .card {
        background: #fff;
        border-color: rgba(0,0,0,0.1);
      }
      
      .card-title {
        font-size: 0.75rem;
        font-weight: 600;
        color: #7ecce0;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(126, 204, 224, 0.2);
      }
      
      /* Details Card */
      .detail-item {
        display: flex;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(126, 204, 224, 0.1);
      }
      
      .detail-item:last-child {
        border-bottom: none;
      }
      
      .detail-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(126, 204, 224, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #7ecce0;
        font-size: 1rem;
        flex-shrink: 0;
      }
      
      .detail-content {
        flex: 1;
      }
      
      .detail-label {
        font-size: 0.75rem;
        color: rgba(126, 204, 224, 0.7);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
      }
      
      .detail-value {
        font-size: 1rem;
        color: #e6f7ff;
        font-weight: 500;
      }
      
      [data-theme="light"] .detail-value {
        color: #1a1a1a;
      }
      
      .detail-value .open-badge {
        color: #22c55e;
        margin-left: 0.5rem;
      }
      
      /* Facilities Grid */
      .facilities-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
      }
      
      .facility-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem;
        background: rgba(126, 204, 224, 0.05);
        border-radius: 12px;
        border: 1px solid rgba(126, 204, 224, 0.1);
      }
      
      .facility-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
      }
      
      .facility-name {
        font-size: 0.75rem;
        color: rgba(230, 247, 255, 0.7);
        text-align: center;
      }
      
      [data-theme="light"] .facility-name {
        color: #666;
      }
      
      /* Weather Card */
      .weather-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
      }
      
      .weather-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
      }
      
      .weather-main {
        flex: 1;
      }
      
      .weather-temp {
        font-size: 3rem;
        font-weight: 600;
        line-height: 1;
        margin-bottom: 0.25rem;
      }
      
      .weather-temp sup {
        font-size: 1.5rem;
        font-weight: 400;
      }
      
      .weather-desc {
        color: rgba(230, 247, 255, 0.7);
        font-size: 0.875rem;
      }
      
      [data-theme="light"] .weather-desc {
        color: #666;
      }
      
      .live-badge {
        padding: 0.25rem 0.75rem;
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
      }
      
      .weather-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
      }
      
      .stat-box {
        padding: 1rem;
        background: rgba(126, 204, 224, 0.05);
        border-radius: 12px;
        text-align: center;
        border: 1px solid rgba(126, 204, 224, 0.1);
      }
      
      .stat-value {
        font-size: 1.25rem;
        font-weight: 600;
        color: #e6f7ff;
        margin-bottom: 0.25rem;
      }
      
      [data-theme="light"] .stat-value {
        color: #1a1a1a;
      }
      
      .stat-unit {
        font-size: 0.875rem;
        color: #7ecce0;
      }
      
      .stat-label {
        font-size: 0.625rem;
        color: rgba(126, 204, 224, 0.6);
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
      
      .forecast-row {
        display: flex;
        gap: 0.75rem;
      }
      
      .forecast-item {
        flex: 1;
        padding: 0.75rem;
        background: rgba(126, 204, 224, 0.05);
        border-radius: 12px;
        text-align: center;
        border: 1px solid rgba(126, 204, 224, 0.1);
      }
      
      .forecast-day {
        font-size: 0.625rem;
        color: rgba(126, 204, 224, 0.6);
        text-transform: uppercase;
        margin-bottom: 0.5rem;
      }
      
      .forecast-icon {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
      }
      
      .forecast-temp {
        font-size: 1rem;
        font-weight: 600;
        color: #e6f7ff;
      }
      
      [data-theme="light"] .forecast-temp {
        color: #1a1a1a;
      }
      
      .forecast-temp span {
        color: rgba(126, 204, 224, 0.6);
        font-size: 0.75rem;
      }
      
      /* Virtual Tour Card */
      .virtual-tour-card {
        padding: 0;
        overflow: hidden;
      }
      
      .virtual-tour-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem;
        background: rgba(126, 204, 224, 0.05);
        border-bottom: 1px solid rgba(126, 204, 224, 0.1);
      }
      
      .virtual-tour-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.25rem;
        font-weight: 500;
        color: #e6f7ff;
      }
      
      .virtual-tour-title em {
        color: #7ecce0;
        font-style: italic;
      }
      
      [data-theme="light"] .virtual-tour-title {
        color: #1a1a1a;
      }
      
      .virtual-tour-icon {
        width: 36px;
        height: 36px;
        background: rgba(126, 204, 224, 0.15);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
      }
      
      .interactive-badge {
        padding: 0.375rem 0.875rem;
        background: rgba(126, 204, 224, 0.15);
        border-radius: 20px;
        font-size: 0.75rem;
        color: #7ecce0;
        font-weight: 500;
      }
      
      .virtual-tour-container {
        position: relative;
        height: 300px;
        background: linear-gradient(180deg, rgba(13, 29, 40, 0.9) 0%, rgba(21, 34, 53, 0.95) 100%);
        overflow: hidden;
      }
      
      [data-theme="light"] .virtual-tour-container {
        background: linear-gradient(180deg, rgba(42, 157, 184, 0.1) 0%, rgba(42, 157, 184, 0.05) 100%);
      }
      
      .view-badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(0, 0, 0, 0.6);
        border-radius: 20px;
        font-size: 0.875rem;
        color: #e6f7ff;
        z-index: 10;
      }
      
      .view-icon {
        font-size: 1rem;
      }
      
      .tour-iframe-wrapper {
        width: 100%;
        height: 100%;
        position: relative;
      }
      
      .tour-iframe-wrapper iframe {
        width: 100%;
        height: 100%;
        border: none;
      }
      
      .drag-hint {
        position: absolute;
        bottom: 1rem;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1rem;
        background: rgba(0, 0, 0, 0.7);
        border-radius: 25px;
        font-size: 0.875rem;
        color: rgba(230, 247, 255, 0.9);
        z-index: 10;
      }
      
      .drag-icon {
        font-size: 1rem;
      }
      
      .virtual-tour-controls {
        display: flex;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        background: rgba(126, 204, 224, 0.05);
        border-top: 1px solid rgba(126, 204, 224, 0.1);
      }
      
      .tour-btn {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.375rem;
        padding: 0.75rem 0.5rem;
        background: rgba(126, 204, 224, 0.1);
        border: 1px solid rgba(126, 204, 224, 0.2);
        border-radius: 10px;
        color: #7ecce0;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
      }
      
      .tour-btn i {
        font-size: 1rem;
      }
      
      .tour-btn:hover {
        background: rgba(126, 204, 224, 0.2);
        border-color: rgba(126, 204, 224, 0.4);
      }
      
      .tour-btn.primary {
        background: #7ecce0;
        border-color: #7ecce0;
        color: #0a0f1a;
      }
      
      .tour-btn.primary:hover {
        background: #5fb8c4;
        border-color: #5fb8c4;
      }
      
      [data-theme="light"] .tour-btn.primary {
        background: #2a9db8;
        border-color: #2a9db8;
        color: #fff;
      }
      
      [data-theme="light"] .tour-btn.primary:hover {
        background: #1a7a8f;
        border-color: #1a7a8f;
      }

      /* Wave Analytics */
      .tide-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
      }
      
      .tide-box {
        padding: 1.25rem;
        background: rgba(126, 204, 224, 0.05);
        border-radius: 12px;
        border: 1px solid rgba(126, 204, 224, 0.1);
      }
      
      .tide-value {
        font-size: 1.75rem;
        font-weight: 600;
        color: #7ecce0;
        margin-bottom: 0.25rem;
      }
      
      .tide-label {
        font-size: 0.75rem;
        color: rgba(126, 204, 224, 0.6);
        text-transform: uppercase;
        margin-bottom: 0.25rem;
      }
      
      .tide-desc {
        font-size: 0.875rem;
        color: rgba(230, 247, 255, 0.6);
      }
      
      [data-theme="light"] .tide-desc {
        color: #888;
      }
      
      .wave-graph {
        height: 80px;
        background: linear-gradient(180deg, rgba(126,204,224,0.1) 0%, rgba(126,204,224,0.02) 100%);
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(126, 204, 224, 0.1);
      }
      
      .wave-graph svg {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
      }
      
      /* Wave Analytics Header */
      .wave-analytics-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
      }
      
      .wave-analytics-header .card-title {
        margin-bottom: 0;
      }
      
      .view-tide-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(126, 204, 224, 0.1);
        border: 1px solid rgba(126, 204, 224, 0.3);
        border-radius: 8px;
        color: #7ecce0;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
      }
      
      .view-tide-btn:hover {
        background: rgba(126, 204, 224, 0.2);
        border-color: #7ecce0;
      }
      
      /* Tidal Information Modal */
      .tide-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        justify-content: center;
        align-items: center;
        padding: 2rem;
      }
      
      .tide-modal.active {
        display: flex;
      }
      
      .tide-modal-content {
        background: linear-gradient(135deg, #0f1623 0%, #1a2332 100%);
        border: 1px solid rgba(126, 204, 224, 0.2);
        border-radius: 20px;
        width: 100%;
        max-width: 900px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
      }
      
      .tide-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid rgba(126, 204, 224, 0.1);
      }
      
      .tide-modal-title {
        display: flex;
        align-items: center;
        gap: 1rem;
      }
      
      .tide-modal-title i {
        font-size: 2rem;
        color: #7ecce0;
      }
      
      .tide-title-main {
        font-size: 1.5rem;
        font-weight: 600;
        color: #e6f7ff;
      }
      
      .tide-title-sub {
        font-size: 0.875rem;
        color: rgba(126, 204, 224, 0.7);
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }
      
      .live-dot-inline {
        width: 6px;
        height: 6px;
        background: #22c55e;
        border-radius: 50%;
        display: inline-block;
        animation: pulse 2s infinite;
      }
      
      @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
      }
      
      .tide-modal-close {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(126, 204, 224, 0.1);
        border: 1px solid rgba(126, 204, 224, 0.2);
        color: #7ecce0;
        cursor: pointer;
        transition: all 0.2s ease;
      }
      
      .tide-modal-close:hover {
        background: rgba(126, 204, 224, 0.2);
        border-color: #7ecce0;
      }
      
      .tide-modal-body {
        padding: 2rem;
      }
      
      /* Period Tabs */
      .tide-period-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        background: rgba(126, 204, 224, 0.05);
        padding: 0.5rem;
        border-radius: 12px;
        width: fit-content;
      }
      
      .tide-tab {
        padding: 0.75rem 1.5rem;
        background: transparent;
        border: none;
        border-radius: 8px;
        color: rgba(126, 204, 224, 0.7);
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
      }
      
      .tide-tab:hover {
        color: #7ecce0;
      }
      
      .tide-tab.active {
        background: rgba(126, 204, 224, 0.15);
        color: #7ecce0;
        font-weight: 500;
      }
      
      /* Monthly Grid */
      .tide-monthly-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
      }
      
      .tide-month-card {
        background: rgba(126, 204, 224, 0.05);
        border: 1px solid rgba(126, 204, 224, 0.1);
        border-radius: 12px;
        padding: 1.25rem;
        transition: all 0.2s ease;
      }
      
      .tide-month-card:hover {
        background: rgba(126, 204, 224, 0.1);
        border-color: rgba(126, 204, 224, 0.2);
      }
      
      .tide-month-name {
        font-size: 1rem;
        font-weight: 600;
        color: #7ecce0;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }
      
      .tide-month-year {
        font-size: 0.75rem;
        font-weight: 400;
        color: rgba(126, 204, 224, 0.6);
        background: rgba(126, 204, 224, 0.1);
        padding: 0.125rem 0.5rem;
        border-radius: 4px;
      }
      
      .tide-month-dates {
        font-size: 0.8rem;
        color: rgba(230, 247, 255, 0.7);
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(126, 204, 224, 0.1);
      }
      
      [data-theme="light"] .tide-month-dates {
        color: #666;
        border-color: rgba(0, 0, 0, 0.1);
      }
      
      .tide-dates-list {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.2rem;
        margin-bottom: 1rem;
        padding: 0.5rem;
        background: rgba(126, 204, 224, 0.05);
        border-radius: 8px;
        border: 1px solid rgba(126, 204, 224, 0.1);
      }
      
      .tide-date-item {
        text-align: center;
        padding: 1rem;
        background: rgba(126, 204, 224, 0.1);
        border: 1px solid rgba(126, 204, 224, 0.2);
        border-radius: 4px;
        transition: all 0.2s ease;
        aspect-ratio: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 85px;
      }
      
      .tide-date-item:hover {
        background: rgba(126, 204, 224, 0.2);
        transform: scale(1.05);
      }
      
      .tide-date {
        font-size: 1rem;
        font-weight: 700;
        color: #7ecce0;
        margin-bottom: 0.3rem;
      }
      
      .tide-date-tides {
        font-size: 0.9rem;
        line-height: 1.2;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        font-weight: 700;
      }
      
      .tide-high {
        color: #ff9f43;
        font-weight: 900;
        font-size: 1rem;
      }
      
      .tide-low {
        color: #54a0ff;
        font-weight: 900;
        font-size: 1rem;
      }
      
      .tide-more-dates {
        text-align: center;
        font-size: 0.8rem;
        color: rgba(230, 247, 255, 0.6);
        margin-bottom: 0.75rem;
        font-style: italic;
      }
      
      .tide-month-summary {
        border-top: 1px solid rgba(126, 204, 224, 0.1);
        padding-top: 0.75rem;
      }
      
      [data-theme="light"] .tide-dates-list {
        background: rgba(0, 0, 0, 0.02);
        border-color: rgba(0, 0, 0, 0.1);
      }
      
      [data-theme="light"] .tide-date-item {
        background: rgba(0, 0, 0, 0.05);
        border-color: rgba(0, 0, 0, 0.1);
      }
      
      [data-theme="light"] .tide-date-item:hover {
        background: rgba(0, 0, 0, 0.1);
      }
      
      [data-theme="light"] .tide-more-dates {
        color: #666;
      }
      
      [data-theme="light"] .tide-month-summary {
        border-color: rgba(0, 0, 0, 0.1);
      }
      
      .tide-month-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
      }
      
      .tide-month-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        color: rgba(230, 247, 255, 0.7);
      }
      
      .tide-month-row span:last-child {
        color: #7ecce0;
        font-weight: 500;
      }
      
      /* Detailed Stats */
      .tide-detailed-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
      }
      
      .tide-stat-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        background: rgba(126, 204, 224, 0.05);
        border: 1px solid rgba(126, 204, 224, 0.1);
        border-radius: 12px;
        padding: 1.25rem;
      }
      
      .tide-stat-icon {
        font-size: 1.5rem;
      }
      
      .tide-stat-info {
        display: flex;
        flex-direction: column;
      }
      
      .tide-stat-value {
        font-size: 1.25rem;
        font-weight: 600;
        color: #7ecce0;
      }
      
      .tide-stat-label {
        font-size: 0.75rem;
        color: rgba(126, 204, 224, 0.6);
        text-transform: uppercase;
      }
      
      /* Tide Chart */
      .tide-chart-container {
        background: rgba(126, 204, 224, 0.05);
        border: 1px solid rgba(126, 204, 224, 0.1);
        border-radius: 12px;
        padding: 1.5rem;
      }
      
      .tide-chart-title {
        font-size: 0.9rem;
        color: rgba(126, 204, 224, 0.8);
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
      }
      
      .tide-chart {
        height: 150px;
        position: relative;
        overflow: hidden;
      }
      
      .tide-chart svg {
        width: 100%;
        height: 100%;
      }
      
      .tide-chart-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 0.5rem;
        font-size: 0.75rem;
        color: rgba(126, 204, 224, 0.5);
      }
      
      /* Light theme adjustments */
      [data-theme="light"] .tide-modal-content {
        background: linear-gradient(135deg, #fff 0%, #f0f4f8 100%);
        border-color: rgba(0, 0, 0, 0.1);
      }
      
      [data-theme="light"] .tide-title-main {
        color: #1a1a1a;
      }
      
      [data-theme="light"] .tide-month-card,
      [data-theme="light"] .tide-stat-card,
      [data-theme="light"] .tide-chart-container {
        background: rgba(0, 0, 0, 0.03);
        border-color: rgba(0, 0, 0, 0.1);
      }
      
      [data-theme="light"] .tide-month-row {
        color: #666;
      }
      
      [data-theme="light"] .tide-tab.active {
        background: rgba(42, 157, 184, 0.15);
      }
      
      /* Responsive */
      @media (max-width: 768px) {
        .tide-modal {
          padding: 1rem;
        }
        
        .tide-detailed-stats {
          grid-template-columns: repeat(2, 1fr);
        }
        
        .tide-monthly-grid {
          grid-template-columns: 1fr;
        }
        
        .tide-modal-body {
          padding: 1rem;
        }
      }
      
      /* Map Card */
      .map-container {
        height: 200px;
        background: rgba(126, 204, 224, 0.05);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        border: 1px solid rgba(126, 204, 224, 0.1);
        position: relative;
      }
      
      .map-pin {
        width: 50px;
        height: 50px;
        background: rgba(126, 204, 224, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #7ecce0;
      }
      
      .map-location {
        font-size: 1rem;
        color: #e6f7ff;
        margin-bottom: 0.25rem;
      }
      
      [data-theme="light"] .map-location {
        color: #1a1a1a;
      }
      
      .map-coords {
        font-size: 0.75rem;
        color: rgba(126, 204, 224, 0.6);
        margin-bottom: 1rem;
      }
      
      .btn-map {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.75rem;
        background: rgba(126, 204, 224, 0.1);
        border: 1px solid rgba(126, 204, 224, 0.2);
        border-radius: 8px;
        color: #7ecce0;
        font-size: 0.875rem;
        cursor: pointer;
      }
      
      /* AI Assistant */
      .ai-badge {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
      }
      
      .ai-dot {
        width: 8px;
        height: 8px;
        background: #22c55e;
        border-radius: 50%;
      }
      
      .ai-badge span {
        font-size: 0.75rem;
        color: #7ecce0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
      
      .ai-message {
        font-size: 1rem;
        line-height: 1.6;
        color: #e6f7ff;
        margin-bottom: 1rem;
      }
      
      [data-theme="light"] .ai-message {
        color: #1a1a1a;
      }
      
      .ai-questions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
      }
      
      .ai-question {
        padding: 0.75rem 1rem;
        background: rgba(126, 204, 224, 0.05);
        border-radius: 8px;
        font-size: 0.875rem;
        color: rgba(230, 247, 255, 0.7);
        cursor: pointer;
        border: 1px solid rgba(126, 204, 224, 0.1);
      }
      
      [data-theme="light"] .ai-question {
        color: #666;
      }
      
      /* Nearby Beaches */
      .nearby-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
      }
      
      .nearby-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(126, 204, 224, 0.05);
        border-radius: 12px;
        border: 1px solid rgba(126, 204, 224, 0.1);
      }
      
      .nearby-thumb {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        background: rgba(126, 204, 224, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
      }
      
      .nearby-info {
        flex: 1;
      }
      
      .nearby-name {
        font-size: 1rem;
        font-weight: 500;
        color: #e6f7ff;
        margin-bottom: 0.25rem;
      }
      
      [data-theme="light"] .nearby-name {
        color: #1a1a1a;
      }
      
      .nearby-meta {
        font-size: 0.75rem;
        color: rgba(126, 204, 224, 0.6);
      }
      
      .nearby-meta .stars {
        font-size: 0.625rem;
        margin-left: 0.5rem;
      }
      
      .nearby-distance {
        font-size: 0.875rem;
        color: #7ecce0;
        display: flex;
        align-items: center;
        gap: 0.25rem;
      }
      
      /* Responsive */
      @media (max-width: 1024px) {
        .main-content {
          grid-template-columns: 1fr;
        }
        
        .hero-content {
          flex-direction: column;
          align-items: flex-start;
          gap: 1.5rem;
        }
        
        .facilities-grid {
          grid-template-columns: repeat(2, 1fr);
        }
        
        .weather-stats {
          grid-template-columns: repeat(2, 1fr);
        }
      }
      
      @media (max-width: 768px) {
        .top-nav {
          padding: 1rem;
        }
        
        .nav-links {
          display: none;
        }
        
        .hero-section {
          height: 350px;
          padding: 1.5rem;
        }
        
        .beach-title {
          font-size: 2rem;
        }
        
        .main-content {
          padding: 0 1rem 1rem;
        }
        
        .photo-gallery {
          display: none;
        }
      }
    </style>
  </head>
  <body>
    <!-- Top Navigation -->
    <nav class="top-nav">
      <div class="nav-left">
        <div class="logo">Dagat Ta <span>bAI</span></div>
        <div class="nav-links">
          <a href="/explore">MAP</a>
          <a href="#nearby-beaches">NEAR BEACHES</a>
          <a href="#weather">WEATHER</a>
          <a href="#ai-assistant">AI GUIDE</a>
        </div>
      </div>
      <div class="nav-right">
        <div class="theme-toggle" onclick="toggleTheme()">
          <i class="fa-solid fa-moon"></i>
          <span>Dark</span>
        </div>
        <a href="/explore" class="back-btn">
          <i class="fa-solid fa-arrow-left"></i>
          Back to Map
        </a>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-content">
        <div class="hero-left">
          <div class="hero-tags">
            <span class="tag">🏖️ RESORT</span>
            <span class="tag">🤿 SNORKELING</span>
            <span class="tag open">● Open Now</span>
          </div>
          <h1 class="beach-title" id="beach-name">Sta. Rosa <span>Beach</span></h1>
          <div class="hero-rating">
            <span class="stars">
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
            </span>
            <span class="rating-text">4.8 <span>(124 reviews)</span></span>
          </div>
          <div class="hero-location">
            <i class="fa-solid fa-location-dot"></i>
            <span id="beach-address">Barangay Binongkalan, Catmon, Cebu</span>
          </div>
        </div>
        <div class="hero-right">
          <div class="photo-gallery">
            <div class="photo-thumb">📸</div>
            <div class="photo-thumb">📸</div>
            <div class="photo-thumb">📸</div>
            <div class="photo-more">+5 more</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Action Bar -->
    <div class="action-bar">
      <button class="btn-save" onclick="alert('Save location coming soon!')">
        <i class="fa-solid fa-location-dot"></i>
        Save Location
      </button>
      <button class="btn-share" onclick="shareBeach()">
        <i class="fa-solid fa-share"></i>
        Share
      </button>
    </div>

    <!-- Main Content Grid -->
    <div class="main-content">
      <!-- Left Column -->
      <div class="left-column">
        <!-- Details Card -->
        <div class="card">
          <div class="card-title">Details</div>
          <div class="detail-item">
            <div class="detail-icon">⏰</div>
            <div class="detail-content">
              <div class="detail-label">Operating Hours</div>
              <div class="detail-value">6:00 AM – 10:00 PM <span class="open-badge">● Open Now</span></div>
            </div>
          </div>
          <div class="detail-item">
            <div class="detail-icon">📞</div>
            <div class="detail-content">
              <div class="detail-label">Contact Number</div>
              <div class="detail-value">+63 962 123 4567</div>
            </div>
          </div>
          <div class="detail-item">
            <div class="detail-icon">📍</div>
            <div class="detail-content">
              <div class="detail-label">Address</div>
              <div class="detail-value">2 km via N. Bacalso Ave., Barangay Binongkalan, Catmon, Cebu</div>
            </div>
          </div>
          <div class="detail-item">
            <div class="detail-icon">🎫</div>
            <div class="detail-content">
              <div class="detail-label">Entrance Fees</div>
              <div class="detail-value">Available — Free entrance · Cottage: Small ₱500, Large ₱1,500</div>
            </div>
          </div>
        </div>

        <!-- Facilities Card -->
        <div class="card">
          <div class="card-title">Facilities</div>
          <div class="facilities-grid">
            <div class="facility-item">
              <div class="facility-icon">🪾</div>
              <div class="facility-name">Restrooms</div>
            </div>
            <div class="facility-item">
              <div class="facility-icon">🚿</div>
              <div class="facility-name">Showers</div>
            </div>
            <div class="facility-item">
              <div class="facility-icon">🅿️</div>
              <div class="facility-name">Parking</div>
            </div>
            <div class="facility-item">
              <div class="facility-icon">🏠</div>
              <div class="facility-name">Cottages</div>
            </div>
            <div class="facility-item">
              <div class="facility-icon">🍽️</div>
              <div class="facility-name">Canteen</div>
            </div>
            <div class="facility-item">
              <div class="facility-icon">🤿</div>
              <div class="facility-name">Snorkel Gear</div>
            </div>
            <div class="facility-item">
              <div class="facility-icon">📶</div>
              <div class="facility-name">Wi-Fi</div>
            </div>
            <div class="facility-item">
              <div class="facility-icon">🏊</div>
              <div class="facility-name">Swimming Area</div>
            </div>
          </div>
        </div>

        <!-- Virtual Tour Card -->
        <div class="card virtual-tour-card">
          <div class="virtual-tour-header">
            <div class="virtual-tour-title">
              <div class="virtual-tour-icon">🔮</div>
              <span>360° <em>Virtual Tour</em></span>
            </div>
            <span class="interactive-badge">Interactive</span>
          </div>
          <div class="virtual-tour-container">
            <div class="view-badge">
              <span class="view-icon">🔮</span>
              <span>360° View · <span id="tour-beach-name">Sta. Rosa Beach</span></span>
            </div>
            <div class="tour-iframe-wrapper">
              <iframe id="tour-embeded" name="Tesing2" src="https://tour.panoee.net/iframe/69bbed1a5ab829b008b63cbb" frameBorder="0" width="100%" height="100%" scrolling="no" allowvr="yes" allow="vr; xr; accelerometer; gyroscope; autoplay;" allowFullScreen="false" webkitallowfullscreen="false" mozallowfullscreen="false" loading="lazy"></iframe>
            </div>
            <div class="drag-hint">
              <span class="drag-icon">☝️</span>
              <span>Click and drag to look around</span>
            </div>
          </div>
          <div class="virtual-tour-controls">
            <button class="tour-btn" onclick="resetView()">
              <i class="fa-solid fa-rotate-left"></i>
              <span>Reset<br>View</span>
            </button>
            <button class="tour-btn" onclick="zoomIn()">
              <i class="fa-solid fa-magnifying-glass-plus"></i>
              <span>Zoom<br>In</span>
            </button>
            <button class="tour-btn" onclick="zoomOut()">
              <i class="fa-solid fa-magnifying-glass-minus"></i>
              <span>Zoom<br>Out</span>
            </button>
            <button class="tour-btn primary" onclick="toggleFullscreen()">
              <i class="fa-solid fa-expand"></i>
              <span>Full<br>Screen</span>
            </button>
          </div>
        </div>

        <!-- Weather Card -->
        <div class="card" id="weather">
            <div class="weather-header">
              <div class="weather-icon">⛅</div>
              <div class="weather-main">
                <div class="weather-temp" id="weather-temp">31<sup>°</sup></div>
                <div class="weather-desc">Partly Cloudy · <span id="weather-location">Binongkalan</span></div>
              </div>
              <div class="live-badge">● Live</div>
            </div>
            <div class="weather-stats">
              <div class="stat-box">
                <div class="stat-value" id="wave-height">0.4</div>
                <div class="stat-unit">m</div>
                <div class="stat-label">Wave Ht.</div>
              </div>
              <div class="stat-box">
                <div class="stat-value" id="wind-speed">12</div>
                <div class="stat-unit">km/h</div>
                <div class="stat-label">Wind</div>
              </div>
              <div class="stat-box">
                <div class="stat-value" id="humidity">78</div>
                <div class="stat-unit">%</div>
                <div class="stat-label">Humidity</div>
              </div>
              <div class="stat-box">
                <div class="stat-value" id="uv-index">7</div>
                <div class="stat-unit">UV</div>
                <div class="stat-label">Index</div>
              </div>
            </div>
            <div class="forecast-row">
              <div class="forecast-item">
                <div class="forecast-day">Today</div>
                <div class="forecast-icon">⛅</div>
                <div class="forecast-temp">31° <span>26°</span></div>
              </div>
              <div class="forecast-item">
                <div class="forecast-day">Fri</div>
                <div class="forecast-icon">☀️</div>
                <div class="forecast-temp">33° <span>27°</span></div>
              </div>
              <div class="forecast-item">
                <div class="forecast-day">Sat</div>
                <div class="forecast-icon">🌦️</div>
                <div class="forecast-temp">28° <span>24°</span></div>
              </div>
              <div class="forecast-item">
                <div class="forecast-day">Sun</div>
                <div class="forecast-icon">⛅</div>
                <div class="forecast-temp">30° <span>25°</span></div>
              </div>
              <div class="forecast-item">
                <div class="forecast-day">Mon</div>
                <div class="forecast-icon">☀️</div>
                <div class="forecast-temp">34° <span>27°</span></div>
              </div>
            </div>
          </div>
      </div>

      <!-- Right Column -->
      <div class="right-column">
        <!-- Map Card -->
        <div class="card">
          <div class="card-title">Location</div>
          <div class="map-container">
            <div class="map-pin">📍</div>
          </div>
          <div class="map-location" id="map-location">Barangay Binongkalan, Catmon, Cebu</div>
          <div class="map-coords" id="map-coords">10.6120, 124.0120</div>
          <button class="btn-map">
            <i class="fa-solid fa-map"></i>
            Open in Full Map
          </button>
        </div>

        <!-- AI Assistant -->
        <div class="card" id="ai-assistant">
          <div class="ai-badge">
            <div class="ai-dot"></div>
            <span>AI Assistant</span>
          </div>
          <div class="ai-message">
            "Best time to visit <span id="ai-beach-name">Sta. Rosa Beach</span> today is 7–10am. Waves are calm at 0.4m — great for swimming!"
          </div>
          <div class="ai-questions">
            <div class="ai-question">🎧 Is it safe to swim today?</div>
            <div class="ai-question">🏠 What facilities are available?</div>
            <div class="ai-question">🚗 How do I get there?</div>
          </div>
        </div>

        <!-- Nearby Beaches -->
        <div class="card" id="nearby-beaches">
          <div class="card-title">Nearby Beaches</div>
          <div class="nearby-list" id="nearby-list">
            <!-- Nearby beaches will be loaded dynamically -->
          </div>
        </div>

        <!-- Wave Analytics -->
        <div class="card" id="wave-analytics">
          <div class="wave-analytics-header">
            <div class="card-title">Wave Analytics</div>
            <button class="view-tide-btn" onclick="openTideModal()">
              <i class="fa-solid fa-chart-line"></i>
              View Details
            </button>
          </div>
          <div class="tide-grid">
            <div class="tide-box">
              <div class="tide-value" id="current-low">0.2 m</div>
              <div class="tide-label">Low Tide</div>
              <div class="tide-desc" id="low-tide-desc">Calm conditions</div>
            </div>
            <div class="tide-box">
              <div class="tide-value" id="current-high">1.1 m</div>
              <div class="tide-label">High Tide</div>
              <div class="tide-desc" id="high-tide-desc">Afternoon peak</div>
            </div>
          </div>
          <div class="wave-graph">
            <svg viewBox="0 0 400 80" preserveAspectRatio="none">
              <path d="M0,60 Q50,30 100,60 T200,50 T300,60 T400,40" fill="none" stroke="#7ecce0" stroke-width="2"/>
              <circle cx="150" cy="45" r="4" fill="#7ecce0"/>
              <text x="150" y="35" fill="#7ecce0" font-size="10" text-anchor="middle">1.0m peak</text>
            </svg>
          </div>
        </div>

        <!-- Tidal Information Modal -->
        <div id="tide-modal" class="tide-modal">
          <div class="tide-modal-content">
            <div class="tide-modal-header">
              <div class="tide-modal-title">
                <i class="fa-solid fa-water"></i>
                <div>
                  <div class="tide-title-main">Tidal Information</div>
                  <div class="tide-title-sub">Real-time tide predictions • <span class="live-dot-inline"></span> Live</div>
                </div>
              </div>
              <button class="tide-modal-close" onclick="closeTideModal()">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
            
            <div class="tide-modal-body">
              <!-- Period Selector -->
              <div class="tide-period-tabs">
                <button class="tide-tab active" onclick="switchTidePeriod(30)">1 Month</button>
                <button class="tide-tab" onclick="switchTidePeriod(365)">1 Year</button>
              </div>
              
              <!-- Monthly Summary Grid -->
              <div class="tide-monthly-grid" id="tide-monthly-grid">
                <!-- Monthly data will be loaded here -->
              </div>
              
              <!-- Detailed Stats -->
              <div class="tide-detailed-stats">
                <div class="tide-stat-card">
                  <div class="tide-stat-icon">📈</div>
                  <div class="tide-stat-info">
                    <div class="tide-stat-value" id="stat-highest">-- m</div>
                    <div class="tide-stat-label">Highest Tide</div>
                  </div>
                </div>
                <div class="tide-stat-card">
                  <div class="tide-stat-icon">📉</div>
                  <div class="tide-stat-info">
                    <div class="tide-stat-value" id="stat-lowest">-- m</div>
                    <div class="tide-stat-label">Lowest Tide</div>
                  </div>
                </div>
                <div class="tide-stat-card">
                  <div class="tide-stat-icon">📊</div>
                  <div class="tide-stat-info">
                    <div class="tide-stat-value" id="stat-average">-- m</div>
                    <div class="tide-stat-label">Average Range</div>
                  </div>
                </div>
                <div class="tide-stat-card">
                  <div class="tide-stat-icon">🌊</div>
                  <div class="tide-stat-info">
                    <div class="tide-stat-value" id="stat-total">--</div>
                    <div class="tide-stat-label">Total Tides</div>
                  </div>
                </div>
              </div>
              
              <!-- Tide Chart -->
              <div class="tide-chart-container">
                <div class="tide-chart-title">Tide Height Predictions</div>
                <div class="tide-chart" id="tide-chart">
                  <svg viewBox="0 0 800 200" preserveAspectRatio="none">
                    <path id="tide-chart-path" d="M0,100 Q100,50 200,100 T400,100 T600,100 T800,100" fill="none" stroke="#7ecce0" stroke-width="2"/>
                  </svg>
                </div>
                <div class="tide-chart-labels" id="tide-chart-labels">
                  <!-- Month labels will be added here -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      var beachId = {{ $id }};
      
      function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
      }
      
      function shareBeach() {
        if (navigator.share) {
          navigator.share({
            title: document.getElementById('beach-name').textContent,
            text: 'Check out this beach!',
            url: window.location.href
          }).catch(function() {});
        } else {
          alert('Share: ' + document.getElementById('beach-name').textContent);
        }
      }
      
      fetch('/api/locations/' + beachId)
        .then(function(res) { return res.json(); })
        .then(function(data) {
          if (data) {
            document.getElementById('beach-name').textContent = data.name || 'Unknown Beach';
            document.getElementById('beach-address').textContent = data.address || '—';
            document.getElementById('ai-beach-name').textContent = data.name || 'this beach';
            document.getElementById('tour-beach-name').textContent = data.name || 'Sta. Rosa Beach';
            document.getElementById('map-location').textContent = data.address || '—';
            if (data.lat && data.lng) {
              document.getElementById('map-coords').textContent = data.lat.toFixed(4) + ', ' + data.lng.toFixed(4);
              fetchWeather(data.lat, data.lng);
            }
            // Fetch nearby beaches after loading current beach data
            fetchNearbyBeaches();
          }
        })
        .catch(function() {
          var demoData = [
            { id: 1, name: 'HINAGDANAN Beach', address: 'Dauis, Panglao Island, Bohol', lat: 9.6, lng: 123.85 },
            { id: 2, name: 'RANILA Beach Resort', address: 'Catmon, Cebu', lat: 10.7, lng: 124.0 },
            { id: 3, name: 'TURTLE POINT', address: 'Moalboal, Cebu', lat: 9.95, lng: 123.4 },
            { id: 4, name: 'MAJESTIC Beach', address: 'Malapascua Island, Cebu', lat: 11.3, lng: 124.1 },
            { id: 5, name: 'Binongkalan Beach', address: 'Binongkalan, Catmon, Cebu', lat: 10.7, lng: 124.0 },
            { id: 6, name: 'Guiwanon Beach', address: 'Guiwanon, Catmon, Cebu', lat: 10.7, lng: 124.0 },
            { id: 7, name: 'San Roque Beach', address: 'San Roque, Catmon, Cebu', lat: 10.7, lng: 124.0 }
          ];
          
          var beach = demoData.find(function(b) { return b.id == beachId; });
          if (beach) {
            document.getElementById('beach-name').textContent = beach.name;
            document.getElementById('beach-address').textContent = beach.address;
            document.getElementById('ai-beach-name').textContent = beach.name;
            document.getElementById('tour-beach-name').textContent = beach.name;
            document.getElementById('map-location').textContent = beach.address;
            document.getElementById('map-coords').textContent = beach.lat.toFixed(4) + ', ' + beach.lng.toFixed(4);
            fetchWeather(beach.lat, beach.lng);
          }
          // Load demo nearby beaches
          loadDemoNearbyBeaches();
        });
      
      function fetchNearbyBeaches() {
        fetch('/api/locations/' + beachId + '/nearby')
          .then(function(res) { return res.json(); })
          .then(function(beaches) {
            var container = document.getElementById('nearby-list');
            if (!beaches || beaches.length === 0) {
              container.innerHTML = '<div style="padding: 1rem; text-align: center; color: rgba(230,247,255,0.6);">No nearby beaches found</div>';
              return;
            }
            
            container.innerHTML = beaches.map(function(beach) {
              var stars = '';
              var fullStars = Math.floor(beach.rating || 4);
              for (var i = 0; i < 5; i++) {
                stars += i < fullStars ? '★' : '☆';
              }
              
              var icon = '🏖️';
              if (beach.type === 'Resort') icon = '🌴';
              else if (beach.type === 'Snorkel') icon = '🌊';
              else if (beach.type === 'Public') icon = '🏖️';
              
              var imageHtml = beach.image 
                ? '<img src="' + beach.image + '" alt="' + beach.name + '" style="width: 50px; height: 50px; border-radius: 10px; object-fit: cover;">' 
                : '<div class="nearby-thumb">' + icon + '</div>';
              
              return '<a href="/beach/' + beach.id + '" class="nearby-item" style="text-decoration: none; color: inherit;">' +
                imageHtml +
                '<div class="nearby-info">' +
                  '<div class="nearby-name">' + beach.name + '</div>' +
                  '<div class="nearby-meta">' + beach.type + ' · <span class="stars">' + stars + '</span></div>' +
                '</div>' +
                '<div class="nearby-distance">' + beach.distance_km + ' →</div>' +
              '</a>';
            }).join('');
          })
          .catch(function(err) {
            console.log('Nearby beaches fetch failed:', err);
            loadDemoNearbyBeaches();
          });
      }
      
      function loadDemoNearbyBeaches() {
        var container = document.getElementById('nearby-list');
        var demoNearby = [
          { name: 'Catmon White Sand', type: 'Public', rating: 4, distance_km: '1.4 km' },
          { name: 'Binongkalan Cove', type: 'Snorkel', rating: 4, distance_km: '2.1 km' },
          { name: 'Coral Bay Resort', type: 'Resort', rating: 5, distance_km: '3.7 km' }
        ];
        
        container.innerHTML = demoNearby.map(function(beach) {
          var stars = '';
          var fullStars = Math.floor(beach.rating);
          for (var i = 0; i < 5; i++) {
            stars += i < fullStars ? '★' : '☆';
          }
          
          var icon = '🏖️';
          if (beach.type === 'Resort') icon = '🌴';
          else if (beach.type === 'Snorkel') icon = '🌊';
          
          return '<div class="nearby-item">' +
            '<div class="nearby-thumb">' + icon + '</div>' +
            '<div class="nearby-info">' +
              '<div class="nearby-name">' + beach.name + '</div>' +
              '<div class="nearby-meta">' + beach.type + ' · <span class="stars">' + stars + '</span></div>' +
            '</div>' +
            '<div class="nearby-distance">' + beach.distance_km + ' →</div>' +
          '</div>';
        }).join('');
      }
      
      function fetchWeather(lat, lon) {
        fetch('/api/weather/comprehensive?lat=' + lat + '&lon=' + lon)
          .then(function(res) { return res.json(); })
          .then(function(data) {
            if (data.current) {
              document.getElementById('weather-temp').innerHTML = Math.round(data.current.temp) + '<sup>°</sup>';
              document.getElementById('weather-desc').textContent = data.current.description || 'Partly Cloudy';
              document.getElementById('wind-speed').textContent = Math.round(data.current.wind_speed || 12);
              document.getElementById('humidity').textContent = data.current.humidity || 78;
            }
            if (data.marine) {
              document.getElementById('wave-height').textContent = (data.marine.wave_height || 0.4).toFixed(1);
            }
          })
          .catch(function(err) {
            console.log('Weather fetch failed:', err);
          });
      }
      
      // Tide Modal Functions
      var currentTidePeriod = 30;
      var beachCoords = { lat: 10.6980, lng: 124.0020 };
      
      function openTideModal() {
        document.getElementById('tide-modal').classList.add('active');
        loadTideData(currentTidePeriod);
      }
      
      function closeTideModal() {
        document.getElementById('tide-modal').classList.remove('active');
      }
      
      function switchTidePeriod(days) {
        currentTidePeriod = days;
        
        // Update tab buttons
        document.querySelectorAll('.tide-tab').forEach(function(tab) {
          tab.classList.remove('active');
        });
        event.target.classList.add('active');
        
        loadTideData(days);
      }
      
      function loadTideData(days) {
        // Show loading state
        document.getElementById('tide-monthly-grid').innerHTML = '<div style="text-align: center; padding: 2rem; color: #7ecce0;">Loading tide data...</div>';
        
        // Fetch tide data from API
        fetch('/api/tides?lat=' + beachCoords.lat + '&lng=' + beachCoords.lng + '&days=' + days)
          .then(function(res) { return res.json(); })
          .then(function(data) {
            displayTideData(data, days);
          })
          .catch(function(err) {
            console.log('Tide fetch failed:', err);
            // Use simulated data
            var simulatedData = generateSimulatedTideData(days);
            displayTideData(simulatedData, days);
          });
      }
      
      function displayTideData(data, days) {
        // Update main stats
        if (data.monthly_stats) {
          document.getElementById('stat-highest').textContent = data.monthly_stats.highest_tide.toFixed(2) + ' m';
          document.getElementById('stat-lowest').textContent = data.monthly_stats.lowest_tide.toFixed(2) + ' m';
          var avgRange = (data.monthly_stats.average_high + data.monthly_stats.average_low) / 2;
          document.getElementById('stat-average').textContent = avgRange.toFixed(2) + ' m';
          var totalTides = (data.high_tides ? data.high_tides.length : 0) + (data.low_tides ? data.low_tides.length : 0);
          document.getElementById('stat-total').textContent = totalTides;
        }
        
        // Generate monthly breakdown
        var monthlyData = generateMonthlyData(data.predictions || [], days);
        displayMonthlyGrid(monthlyData);
        
        // Draw chart
        drawTideChart(data.predictions || [], days);
      }
      
      function generateMonthlyData(predictions, days) {
        var months = {};
        var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        predictions.forEach(function(pred) {
          var date = new Date(pred.time || pred.timestamp * 1000);
          var monthKey = monthNames[date.getMonth()];
          var year = date.getFullYear();
          var monthYearKey = monthKey + ' ' + year;
          var dateStr = monthKey + ' ' + date.getDate();
          
          if (!months[monthYearKey]) {
            months[monthYearKey] = { 
              highs: [], 
              lows: [], 
              count: 0, 
              name: monthKey,
              year: year,
              dates: {},
              startDate: date,
              endDate: date
            };
          }
          
          // Track date range
          if (date < months[monthYearKey].startDate) {
            months[monthYearKey].startDate = date;
          }
          if (date > months[monthYearKey].endDate) {
            months[monthYearKey].endDate = date;
          }
          
          // Add individual date with tide info
          if (!months[monthYearKey].dates[date.getDate()]) {
            months[monthYearKey].dates[date.getDate()] = {
              date: dateStr,
              fullDate: date,
              high: pred.type === 'High' ? pred.height : null,
              low: pred.type === 'Low' ? pred.height : null
            };
          } else {
            // Update existing date with new tide info
            if (pred.type === 'High') {
              months[monthYearKey].dates[date.getDate()].high = pred.height;
            } else {
              months[monthYearKey].dates[date.getDate()].low = pred.height;
            }
          }
          
          if (pred.type === 'High') {
            months[monthYearKey].highs.push(pred.height);
          } else {
            months[monthYearKey].lows.push(pred.height);
          }
          months[monthYearKey].count++;
        });
        
        // Calculate averages and ensure correct number of days per month
        var result = [];
        Object.keys(months).forEach(function(monthYearKey) {
          var m = months[monthYearKey];
          var avgHigh = m.highs.length ? (m.highs.reduce(function(a,b){return a+b;}, 0) / m.highs.length) : 0;
          var avgLow = m.lows.length ? (m.lows.reduce(function(a,b){return a+b;}, 0) / m.lows.length) : 0;
          
          // Determine correct number of days for this month
          var monthIndex = monthNames.indexOf(m.name);
          var daysInMonth;
          if (monthIndex === 1) { // February
            daysInMonth = 28; // Simplified - not handling leap years
          } else if ([0, 2, 4, 6, 7, 9, 11].includes(monthIndex)) { // Months with 31 days
            daysInMonth = 31;
          } else {
            daysInMonth = 30;
          }
          
          // Create correct number of days for each month
          var allDates = [];
          for (var day = 1; day <= daysInMonth; day++) {
            var dateData = m.dates[day] || {
              date: monthNames[monthIndex] + ' ' + day,
              fullDate: new Date(m.year, monthIndex, day),
              high: null,
              low: null
            };
            allDates.push(dateData);
          }
          
          result.push({
            name: m.name,
            year: m.year,
            dates: allDates,
            avgHigh: avgHigh.toFixed(2),
            avgLow: avgLow.toFixed(2),
            highest: m.highs.length ? Math.max.apply(null, m.highs).toFixed(2) : '--',
            lowest: m.lows.length ? Math.min.apply(null, m.lows).toFixed(2) : '--',
            count: m.count
          });
        });
        
        return result;
      }
      
      function displayMonthlyGrid(monthlyData) {
        var html = monthlyData.map(function(month) {
          var datesHtml = month.dates.map(function(d) {
            var highText = d.high ? d.high + 'm' : '--';
            var lowText = d.low ? d.low + 'm' : '--';
            return '<div class="tide-date-item">' +
              '<div class="tide-date">' + d.date.split(' ')[1] + '</div>' +
              '<div class="tide-date-tides">' +
                '<span class="tide-high">' + highText + '</span>' +
                '<span class="tide-low">' + lowText + '</span>' +
              '</div>' +
            '</div>';
          }).join('');
          
          return '<div class="tide-month-card">' +
            '<div class="tide-month-name">' + month.name + ' <span class="tide-month-year">' + month.year + '</span></div>' +
            '<div class="tide-dates-list">' + datesHtml + '</div>' +
            '<div class="tide-month-summary">' +
              '<div class="tide-month-row"><span>AVG HIGH:</span><span>' + month.avgHigh + ' m</span></div>' +
              '<div class="tide-month-row"><span>AVG LOW:</span><span>' + month.avgLow + ' m</span></div>' +
              '<div class="tide-month-row"><span>HIGHEST:</span><span>' + month.highest + ' m</span></div>' +
              '<div class="tide-month-row"><span>LOWEST:</span><span>' + month.lowest + ' m</span></div>' +
            '</div>' +
          '</div>';
        }).join('');
        
        document.getElementById('tide-monthly-grid').innerHTML = html;
      }
      
      function drawTideChart(predictions, days) {
        if (!predictions || predictions.length === 0) return;
        
        // Sample data points
        var sampled = predictions.filter(function(_, i) { 
          return i % Math.ceil(predictions.length / 50) === 0; 
        }).slice(0, 50);
        
        if (sampled.length < 2) return;
        
        var minHeight = Math.min.apply(null, sampled.map(function(p) { return p.height; }));
        var maxHeight = Math.max.apply(null, sampled.map(function(p) { return p.height; }));
        var range = maxHeight - minHeight || 1;
        
        // Build SVG path
        var points = sampled.map(function(p, i) {
          var x = (i / (sampled.length - 1)) * 800;
          var y = 180 - ((p.height - minHeight) / range) * 160; // Scale to 20-180 range
          return x + ',' + y;
        });
        
        var path = 'M' + points[0];
        for (var i = 1; i < points.length; i++) {
          var prev = points[i - 1].split(',');
          var curr = points[i].split(',');
          var cpx = (parseFloat(prev[0]) + parseFloat(curr[0])) / 2;
          path += ' Q' + cpx + ',' + prev[1] + ' ' + curr[0] + ',' + curr[1];
        }
        
        // Update SVG
        var pathEl = document.getElementById('tide-chart-path');
        if (pathEl) {
          pathEl.setAttribute('d', path);
        }
        
        // Add month labels
        var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var currentMonth = new Date().getMonth();
        var labels = [];
        var numLabels = days > 30 ? 12 : 4;
        for (var i = 0; i < numLabels; i++) {
          var monthIdx = (currentMonth + i) % 12;
          labels.push('<span>' + monthNames[monthIdx] + '</span>');
        }
        document.getElementById('tide-chart-labels').innerHTML = labels.join('');
      }
      
      function generateSimulatedTideData(days) {
        var predictions = [];
        var highTides = [];
        var lowTides = [];
        var now = Date.now() / 1000;
        var hours = days * 24;
        
        var meanHigh = 1.4;
        var meanLow = 0.2;
        var range = meanHigh - meanLow;
        
        for (var i = 0; i < hours; i += 6) {
          var timestamp = now + (i * 3600);
          var dayFraction = (i % 12.42) / 12.42;
          var height = meanLow + (range / 2) + (range / 2) * Math.sin(dayFraction * 2 * Math.PI);
          height += (Math.random() - 0.5) * 0.1;
          height = Math.max(0, height);
          
          var type = height > (meanLow + range / 2) ? 'High' : 'Low';
          var pred = {
            timestamp: timestamp,
            height: parseFloat(height.toFixed(2)),
            type: type,
            time: new Date(timestamp * 1000).toISOString()
          };
          
          predictions.push(pred);
          if (type === 'High') highTides.push(pred);
          else lowTides.push(pred);
        }
        
        var heights = predictions.map(function(p) { return p.height; });
        return {
          predictions: predictions,
          high_tides: highTides,
          low_tides: lowTides,
          monthly_stats: {
            highest_tide: Math.max.apply(null, heights),
            lowest_tide: Math.min.apply(null, heights),
            average_high: highTides.reduce(function(s, p) { return s + p.height; }, 0) / highTides.length,
            average_low: lowTides.reduce(function(s, p) { return s + p.height; }, 0) / lowTides.length
          }
        };
      }
      
      // Close modal on outside click
      document.getElementById('tide-modal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeTideModal();
        }
      });
    </script>
  </body>
</html>
