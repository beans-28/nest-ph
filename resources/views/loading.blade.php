
	<style>
		.app-loading-overlay {
			position: fixed;
			inset: 0;
			display: flex;
			align-items: center;
			justify-content: center;
			background: rgba(255,255,255,0.75);
			z-index: 9999;
		}

		.app-spinner {
			width: 64px;
			height: 64px;
			display: inline-block;
			border-radius: 50%;
			border: 6px solid rgba(0,0,0,0.12);
			border-top-color: rgba(0,0,0,0.65);
			animation: app-spin 0.9s linear infinite;
		}

<<<<<<< HEAD
	app-spin {
			to { transform: rotate(360deg); }
		}
=======
	@keyframes app-spin {
    to { transform: rotate(360deg); }
	}
>>>>>>> 19dca98bca881921813252e5f798da8fb35a8945

		
		.app-spinner--small { width: 32px; height: 32px; border-width:4px; }
		.app-spinner--dark { background: transparent; border-color: rgba(255,255,255,0.2); border-top-color: rgba(255,255,255,0.9); }
	</style>
	


<<<<<<< HEAD
	$overlay = $overlay ?? true;
	$variant = $variant ?? null; // 'small' or 'dark'
	$variantClass = $variant ? 'app-spinner--'.$variant : '';
=======
	@php
    	$overlay = $overlay ?? true;
    	$variant = $variant ?? null;
    	$variantClass = $variant ? 'app-spinner--'.$variant : '';
	@endphp
>>>>>>> 19dca98bca881921813252e5f798da8fb35a8945


	<div class="app-loading-overlay" aria-hidden="false" role="status">
		<div class="app-spinner {{ $variantClass }}" aria-hidden="true"></div>
	</div>
	<div class="app-spinner {{ $variantClass }}" role="status" aria-hidden="true"></div>
