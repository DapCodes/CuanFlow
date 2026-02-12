<?php

use Illuminate\Support\Facades\Broadcast;

// Public channel for production orders - no auth needed
// The channel name includes the outlet_id for multi-tenant isolation
