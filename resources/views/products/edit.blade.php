<x-layout>
    <h1>Edit Product</h1>

    <form action="{{ route('products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required>
            @error('name')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $product->quantity) }}" required min="0">
            @error('quantity')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="expiration_date">Expiration Date:</label>
            <input type="date" name="expiration_date" id="expiration_date" value="{{ old('expiration_date', $product->expiration_date?->format('Y-m-d')) }}">
            @error('expiration_date')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="out_of_stock" {{ old('status', $product->status) == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>
            @error('status')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Tags Section --}}
        <div class="form-group">
            <label for="tag-input">Tags:</label>
            <div class="tags-management">
                {{-- Hidden input to store tag IDs for form submission --}}
                <div id="tags-container">
                    @foreach($product->tags as $tag)
                        <input type="hidden" name="tags[]" value="{{ $tag->name }}">
                    @endforeach
                </div>
                
                {{-- Visual tags display --}}
                <div class="tags-visual-container" id="tags-visual-container">
                    @foreach($product->tags as $tag)
                        <span class="tag" style="background-color: {{ $tag->color }}; color: white;">
                            {{ $tag->name }}
                            <button type="button" class="tag-remove-btn" data-tag-name="{{ $tag->name }}">×</button>
                        </span>
                    @endforeach
                </div>

                {{-- Tag input with autocomplete --}}
                <div class="tag-input-container">
                    <input type="text" id="tag-input" class="tag-input" placeholder="Type tag name and press Enter...">
                    <div id="tag-suggestions" class="tag-suggestions"></div>
                </div>
                <small class="form-text">Type a tag name and press Enter to add it. Use backspace to remove last tag.</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="{{ route('products.show', $product) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
    
    <script>
        class TagManager {
            constructor() {
                this.tagInput = document.getElementById('tag-input');
                this.tagsContainer = document.getElementById('tags-container');
                this.tagsVisualContainer = document.getElementById('tags-visual-container');
                this.suggestionsContainer = document.getElementById('tag-suggestions');
                this.currentTags = new Set();
                
                // Initialize current tags from existing hidden inputs
                document.querySelectorAll('#tags-container input').forEach(input => {
                    this.currentTags.add(input.value);
                });
                
                this.initEventListeners();
            }

            initEventListeners() {
                // Tag input events
                this.tagInput.addEventListener('input', this.handleTagInput.bind(this));
                this.tagInput.addEventListener('keydown', this.handleTagKeydown.bind(this));
                this.tagInput.addEventListener('focus', this.handleTagFocus.bind(this));
                
                // Click outside to close suggestions
                document.addEventListener('click', this.handleClickOutside.bind(this));
            }

            handleTagInput(e) {
                const query = e.target.value.trim();
                
                if (query.length > 1) {
                    this.searchTags(query);
                } else {
                    this.hideSuggestions();
                }
            }

            handleTagKeydown(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.addTag(this.tagInput.value.trim());
                } else if (e.key === 'Backspace' && this.tagInput.value === '') {
                    this.removeLastTag();
                }
            }

            handleTagFocus() {
                if (this.tagInput.value.trim().length > 1) {
                    this.searchTags(this.tagInput.value.trim());
                }
            }

            async searchTags(query) {
                try {
                    const response = await fetch(`/tags/search?query=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (!response.ok) return;
                    
                    const tags = await response.json();
                    this.showSuggestions(tags);
                } catch (error) {
                    console.error('Error searching tags:', error);
                    this.hideSuggestions();
                }
            }

            showSuggestions(tags) {
                this.suggestionsContainer.innerHTML = '';
                
                // Filter out tags that are already added
                const availableTags = tags.filter(tag => !this.currentTags.has(tag.name));
                
                if (availableTags.length === 0) {
                    const suggestion = document.createElement('div');
                    suggestion.className = 'tag-suggestion';
                    suggestion.textContent = 'No matching tags found. Press Enter to create new tag.';
                    this.suggestionsContainer.appendChild(suggestion);
                } else {
                    availableTags.forEach(tag => {
                        const suggestion = document.createElement('div');
                        suggestion.className = 'tag-suggestion';
                        suggestion.textContent = tag.name;
                        suggestion.addEventListener('click', () => this.selectTag(tag.name));
                        this.suggestionsContainer.appendChild(suggestion);
                    });
                }
                
                this.suggestionsContainer.style.display = 'block';
            }

            hideSuggestions() {
                this.suggestionsContainer.style.display = 'none';
            }

            selectTag(tagName) {
                this.tagInput.value = tagName;
                this.addTag(tagName);
                this.hideSuggestions();
            }

            handleClickOutside(e) {
                if (!this.tagInput.contains(e.target) && !this.suggestionsContainer.contains(e.target)) {
                    this.hideSuggestions();
                }
            }

            addTag(tagName) {
                if (!tagName || this.currentTags.has(tagName)) {
                    this.tagInput.value = '';
                    this.hideSuggestions();
                    return;
                }

                // Add to current tags set
                this.currentTags.add(tagName);

                // Create hidden input for form submission
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'tags[]';
                hiddenInput.value = tagName;
                this.tagsContainer.appendChild(hiddenInput);

                // Create visual tag
                const visualTag = document.createElement('span');
                visualTag.className = 'tag';
                visualTag.style.backgroundColor = this.generateRandomColor();
                visualTag.style.color = 'white';
                visualTag.innerHTML = `
                    ${tagName}
                    <button type="button" class="tag-remove-btn" data-tag-name="${tagName}">×</button>
                `;

                // Add remove event listener
                const removeBtn = visualTag.querySelector('.tag-remove-btn');
                removeBtn.addEventListener('click', () => this.removeTag(tagName, hiddenInput, visualTag));

                this.tagsVisualContainer.appendChild(visualTag);
                this.tagInput.value = '';
                this.hideSuggestions();
            }

            removeTag(tagName, hiddenInput, visualTag) {
                this.currentTags.delete(tagName);
                hiddenInput.remove();
                visualTag.remove();
            }

            removeLastTag() {
                if (this.currentTags.size === 0) return;

                const tags = Array.from(this.currentTags);
                const lastTag = tags[tags.length - 1];
                
                const hiddenInput = document.querySelector(`#tags-container input[value="${lastTag}"]`);
                const visualTag = document.querySelector(`#tags-visual-container .tag button[data-tag-name="${lastTag}"]`)?.closest('.tag');
                
                if (hiddenInput && visualTag) {
                    this.removeTag(lastTag, hiddenInput, visualTag);
                }
            }

            generateRandomColor() {
                const colors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ec4899', '#84cc16', '#f97316', '#6366f1'];
                return colors[Math.floor(Math.random() * colors.length)];
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            new TagManager();
        });
    </script>
</x-layout>