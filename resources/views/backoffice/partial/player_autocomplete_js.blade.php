<script>
$(document).ready(function() {
    let debounceTimer;
    let selectedPlayerId = null;
    
    // Initialize with existing player if in edit mode
    const initialPlayerId = $('#player_id').val();
    if (initialPlayerId) {
        selectedPlayerId = initialPlayerId;
    }

    $('#player_search').on('input', function() {
        const searchTerm = $(this).val().trim();
        
        // Clear debounce timer
        clearTimeout(debounceTimer);
        
        // If search term is empty, clear selection
        if (searchTerm === '') {
            $('#player_id').val('');
            selectedPlayerId = null;
            return;
        }
        
        // If search term is less than 2 characters, don't search
        if (searchTerm.length < 2) {
            return;
        }
        
        // Debounce the search - wait 300ms after user stops typing
        debounceTimer = setTimeout(function() {
            searchPlayers(searchTerm);
        }, 300);
    });
    
    // Handle when user focuses out of the input
    $('#player_search').on('blur', function() {
        // Small delay to allow click on suggestion to register
        setTimeout(function() {
            // If no player was selected and input has value, clear it
            if (!selectedPlayerId && $('#player_search').val().trim() !== '') {
                const currentInput = $('#player_search').val().trim();
                // Only clear if the input doesn't match the selected player's name
                if (selectedPlayerId) {
                    // Keep the current display value if a player is selected
                    return;
                } else {
                    $('#player_search').val('');
                    $('#player_id').val('');
                }
            }
        }, 150);
    });
    
    function searchPlayers(term) {
        $.ajax({
            url: '{{ route("api.players.search") }}',
            method: 'GET',
            data: { term: term },
            success: function(players) {
                showSuggestions(players);
            },
            error: function(xhr, status, error) {
                console.error('Error searching players:', error);
            }
        });
    }
    
    function showSuggestions(players) {
        // Remove existing suggestions
        $('.player-suggestions').remove();
        
        if (players.length === 0) {
            return;
        }
        
        // Create suggestions container
        const suggestionsHtml = `
            <div class="player-suggestions" style="position: absolute; background: white; border: 1px solid #ddd; border-top: none; max-height: 200px; overflow-y: auto; width: 100%; z-index: 1000; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                ${players.map(player => `
                    <div class="player-suggestion" data-id="${player.id}" data-text="${player.text}" style="padding: 10px; cursor: pointer; border-bottom: 1px solid #eee; display: flex; align-items: center;">
                        <img src="${player.picture}" alt="${player.text}" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px; object-fit: cover;" onerror="this.src='{{ config('custom.default_profile_pic') }}'">
                        <span>${player.text}</span>
                    </div>
                `).join('')}
            </div>
        `;
        
        // Add suggestions after the input field
        $('#player_search').parent().css('position', 'relative').append(suggestionsHtml);
        
        // Handle suggestion clicks
        $('.player-suggestion').on('click', function() {
            const playerId = $(this).data('id');
            const playerText = $(this).data('text');
            
            $('#player_id').val(playerId);
            $('#player_search').val(playerText);
            selectedPlayerId = playerId;
            
            $('.player-suggestions').remove();
        });
        
        // Handle hover effects
        $('.player-suggestion').on('mouseenter', function() {
            $(this).css('background-color', '#f5f5f5');
        }).on('mouseleave', function() {
            $(this).css('background-color', 'white');
        });
    }
    
    // Close suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#player_search, .player-suggestions').length) {
            $('.player-suggestions').remove();
        }
    });
    
    // Handle keyboard navigation (optional enhancement)
    $('#player_search').on('keydown', function(e) {
        const suggestions = $('.player-suggestion');
        const current = $('.player-suggestion.highlighted');
        
        if (e.keyCode === 40) { // Down arrow
            e.preventDefault();
            if (current.length === 0) {
                suggestions.first().addClass('highlighted').css('background-color', '#e3f2fd');
            } else {
                current.removeClass('highlighted').css('background-color', 'white');
                const next = current.next('.player-suggestion');
                if (next.length) {
                    next.addClass('highlighted').css('background-color', '#e3f2fd');
                } else {
                    suggestions.first().addClass('highlighted').css('background-color', '#e3f2fd');
                }
            }
        } else if (e.keyCode === 38) { // Up arrow
            e.preventDefault();
            if (current.length === 0) {
                suggestions.last().addClass('highlighted').css('background-color', '#e3f2fd');
            } else {
                current.removeClass('highlighted').css('background-color', 'white');
                const prev = current.prev('.player-suggestion');
                if (prev.length) {
                    prev.addClass('highlighted').css('background-color', '#e3f2fd');
                } else {
                    suggestions.last().addClass('highlighted').css('background-color', '#e3f2fd');
                }
            }
        } else if (e.keyCode === 13) { // Enter
            e.preventDefault();
            if (current.length) {
                current.click();
            }
        } else if (e.keyCode === 27) { // Escape
            $('.player-suggestions').remove();
        }
    });
});
</script>
