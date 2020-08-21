<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Game</title>
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
</head>
<body>

    <div x-data="game()" class="flex items-center justify-center min-h-screen">
        <h1 class="fixed top-0 right-0 p-10 font-bold text-3xl">
            <span x-text="points"></span>
            <span class="text-xs">pts</span>
        </h1>
        <h1 class="fixed top-0 left-0 p-10 font-bold- text-2xl">
            <span>Colors Found: </span>
            <span x-text="colorsFound"></span>
        </h1>
        <template x-for="card in cards">
            <div class="w-48 p-5">
                <div x-show="! card.cleared"
                    :style="'background: ' + (card.flipped ? card.color : '#999')" 
                    class="w-full h-64 rounded cursor-pointer"
                    @click="flipCard(card)"
                >
                </div>
            </div>
        </template>
    </div>

    <div x-data="{ show: false, message: 'Default message' }"
        x-show.transition.duration.1000ms="show"
        x-text="message"
        @flash.window="
            message = $event.detail.message; 
            show = true;
            setTimeout(() => show = false, 1000);
        "
        class="fixed bottom-0 right-0 bg-green-500 text-white p-2 mb-4 mr-4 rounded"
    >
    </div>

    <script>
        function flash(message) {
            window.dispatchEvent(new CustomEvent('flash', { 
                detail: { message },
            }));
        };

        function pause(milliseconds = 1000) {
            return new Promise(resolve => setTimeout(resolve, milliseconds));
        };

        function game() {
            return {
                cards: [ 
                    { color: 'green', flipped: false, cleared: false },
                    { color: 'red', flipped: false, cleared: false },
                    { color: 'blue', flipped: false, cleared: false },
                    { color: 'yellow', flipped: false, cleared: false },
                    { color: 'green', flipped: false, cleared: false },
                    { color: 'red', flipped: false, cleared: false },
                    { color: 'blue', flipped: false, cleared: false },
                    { color: 'yellow', flipped: false, cleared: false },
                ].sort(() => Math.random() - .5),

                get cardsCleared() {
                    return ! this.remainingCards.length;
                },

                get colorsFound() {
                    const colors = this.clearedCards.map(o => o.color);

                    return [...new Set(colors)].join(', ');
                },
                
                get clearedCards() {
                    return this.cards.filter(card => card.cleared);
                },

                get flippedCards() {
                    return this.cards.filter(card => card.flipped);
                },

                get points() {
                    return this.clearedCards.length / 2;
                },

                get remainingCards() {
                    return this.cards.filter(card => ! card.cleared);
                },

                async flipCard(card) {
                    if (this.flippedCards.length === 2) {
                        return;
                    }

                    card.flipped = ! card.flipped;

                    if (this.flippedCards.length === 2) {
                        if (this.matchFound()) {
                            flash('You found a match!');

                            await pause();

                            this.flippedCards.forEach(card => { card.cleared = true; card.flipped = false });

                            if (this.cardsCleared) {
                                flash('You won!');
                            }

                            return;
                        }

                        await pause();

                        this.flippedCards.forEach(card => card.flipped = false);
                    }
                },

                matchFound() {
                    if (this.flippedCards.length < 2) {
                        return false;
                    }

                    return this.flippedCards[0]['color'] === this.flippedCards[1]['color'];
                },
            };
        }
    </script>
    
</body>
</html>