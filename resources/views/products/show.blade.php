<x-layout>
    <h1>Product Details</h1>

    {{-- Flash notifications --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="product-details">
        <div class="detail-row">
            <strong>ID:</strong>
            <span>{{ $product->id }}</span>
        </div>

        <div class="detail-row">
            <strong>Name:</strong>
            <span>{{ $product->name }}</span>
        </div>

        <div class="detail-row">
            <strong>Quantity:</strong>
            <span class="quantity-{{ $product->id }}">{{ $product->quantity }}</span>

            {{-- AJAX Increment button --}}
            <button type="button" 
                    class="btn-increase btn-action" 
                    data-product-id="{{ $product->id }}"
                    title="Increase Quantity">
                +
            </button>

            {{-- AJAX Decrement button --}}
            <button type="button" 
                    class="btn-decrease btn-action" 
                    data-product-id="{{ $product->id }}"
                    title="Decrease Quantity">
                -
            </button>
        </div>

        <div class="detail-row">
            <strong>Description:</strong>
            <span>{{ $product->description ?? 'N/A' }}</span>
        </div>

        <div class="detail-row">
            <strong>Expiration Date:</strong>
            <span>{{ $product->expiration_date?->format('Y-m-d') ?? 'N/A' }}</span>
        </div>

        <div class="detail-row">
            <strong>Status:</strong>
            <span class="status-{{ $product->id }}">{{ ucfirst(str_replace('_', ' ', $product->status)) }}</span>
        </div>

        {{-- Tags Display Only --}}
<div class="detail-row">
    <strong>Tags:</strong>
    <div class="tags-container">
        @foreach($product->tags as $tag)
            <span class="tag" style="background-color: {{ $tag->color }}; color: white;">
                {{ $tag->name }}
            </span>
        @endforeach
        @if($product->tags->isEmpty())
            <span class="no-tags">No tags added yet</span>
        @endif
    </div>
    <div style="margin-top: 8px;">
        <small><a href="{{ route('products.edit', $product) }}">Edit tags in edit view</a></small>
    </div>
</div>

        <div class="detail-row">
            <strong>Created:</strong>
            <span>{{ $product->created_at->format('Y-m-d H:i:s') }}</span>
        </div>

        <div class="detail-row">
            <strong>Updated:</strong>
            <span>{{ $product->updated_at->format('Y-m-d H:i:s') }}</span>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('products.edit', $product) }}" class="btn-edit">Edit</a>
        <a href="{{ route('products.index') }}" class="btn-back">Back to List</a>
        
        <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Are you sure you want to delete this product?')" class="btn-delete">Delete</button>
        </form>
    </div>

    <script>
        class TagManager {
            constructor() {
                this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                this.tagInput = document.getElementById('tag-input');
                this.suggestionsContainer = document.getElementById('tag-suggestions');
                this.addTagBtn = document.getElementById('add-tag-btn');
                this.productId = {{ $product->id }};
                
                console.log('TagManager initialized', {
                    productId: this.productId,
                    csrfToken: this.csrfToken ? 'Present' : 'Missing'
                });
                
                this.initEventListeners();
            }

            initEventListeners() {
                // Tag input events
                this.tagInput.addEventListener('input', this.handleTagInput.bind(this));
                this.tagInput.addEventListener('keypress', this.handleTagKeypress.bind(this));
                this.tagInput.addEventListener('focus', this.handleTagFocus.bind(this));
                this.addTagBtn.addEventListener('click', this.addTag.bind(this));
                
                // Click outside to close suggestions
                document.addEventListener('click', this.handleClickOutside.bind(this));

                // Initialize tag remove forms
                this.initializeTagRemoveForms();
            }

            initializeTagRemoveForms() {
                const removeForms = document.querySelectorAll('.tag-remove-form');
                removeForms.forEach(form => {
                    form.addEventListener('submit', this.handleTagRemove.bind(this));
                });
            }

            handleTagInput(e) {
                const query = e.target.value.trim();
                
                if (query.length > 1) {
                    this.searchTags(query);
                } else {
                    this.hideSuggestions();
                }
            }

            handleTagKeypress(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.addTag();
                }
            }

            handleTagFocus() {
                if (this.tagInput.value.trim().length > 1) {
                    this.searchTags(this.tagInput.value.trim());
                }
            }

            async searchTags(query) {
                try {
                    console.log('Searching tags for:', query);
                    const response = await fetch(`/tags/search?query=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const tags = await response.json();
                    console.log('Found tags:', tags);
                    this.showSuggestions(tags);
                } catch (error) {
                    console.error('Error searching tags:', error);
                    this.hideSuggestions();
                }
            }

            showSuggestions(tags) {
                this.suggestionsContainer.innerHTML = '';
                
                if (tags.length === 0) {
                    const suggestion = document.createElement('div');
                    suggestion.className = 'tag-suggestion';
                    suggestion.textContent = 'No tags found. Click "Add Tag" to create new one.';
                    this.suggestionsContainer.appendChild(suggestion);
                } else {
                    tags.forEach(tag => {
                        const suggestion = document.createElement('div');
                        suggestion.className = 'tag-suggestion';
                        suggestion.textContent = tag.name;
                        suggestion.dataset.tagId = tag.id;
                        suggestion.dataset.tagName = tag.name;
                        suggestion.addEventListener('click', () => this.selectTag(tag));
                        this.suggestionsContainer.appendChild(suggestion);
                    });
                }
                
                this.suggestionsContainer.style.display = 'block';
            }

            hideSuggestions() {
                this.suggestionsContainer.style.display = 'none';
            }

            selectTag(tag) {
                this.tagInput.value = tag.name;
                this.hideSuggestions();
            }

            handleClickOutside(e) {
                if (!this.tagInput.contains(e.target) && !this.suggestionsContainer.contains(e.target)) {
                    this.hideSuggestions();
                }
            }

            async addTag() {
                const tagName = this.tagInput.value.trim();
                
                if (!tagName) {
                    this.showFlashMessage('Please enter a tag name', 'error');
                    return;
                }

                console.log('Attempting to add tag:', tagName);

                this.addTagBtn.disabled = true;
                this.addTagBtn.textContent = 'Adding...';

                try {
                    const response = await fetch(`/products/${this.productId}/tags`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name: tagName })
                    });

                    console.log('Response status:', response.status);

                    const data = await response.json();
                    console.log('Response data:', data);

                    if (data.success) {
                        this.addTagToUI(data.tag);
                        this.tagInput.value = '';
                        this.showFlashMessage(data.message, 'success');
                    } else {
                        console.error('Server returned error:', data);
                        this.showFlashMessage(data.message || 'Error adding tag', 'error');
                    }
                } catch (error) {
                    console.error('Network error adding tag:', error);
                    this.showFlashMessage('Network error: ' + error.message, 'error');
                } finally {
                    this.addTagBtn.disabled = false;
                    this.addTagBtn.textContent = 'Add Tag';
                    this.hideSuggestions();
                }
            }

            addTagToUI(tag) {
                const tagsContainer = document.querySelector('.tags-container');
                
                // Remove "no tags" message if present
                const noTagsMessage = tagsContainer.querySelector('.no-tags');
                if (noTagsMessage) {
                    noTagsMessage.remove();
                }
                
                const tagElement = document.createElement('span');
                tagElement.className = 'tag';
                tagElement.style.backgroundColor = tag.color;
                tagElement.style.color = 'white';
                tagElement.innerHTML = `
                    ${tag.name}
                    <form action="/products/${this.productId}/tags/${tag.id}" method="POST" class="tag-remove-form">
                        <input type="hidden" name="_token" value="${this.csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="tag-remove-btn" title="Remove tag">×</button>
                    </form>
                `;

                // Add event listener for the remove button
                const removeForm = tagElement.querySelector('.tag-remove-form');
                removeForm.addEventListener('submit', this.handleTagRemove.bind(this));

                tagsContainer.appendChild(tagElement);
            }

            async handleTagRemove(e) {
                e.preventDefault();
                
                const form = e.target;
                const url = form.action;

                if (!confirm('Are you sure you want to remove this tag?')) {
                    return;
                }

                const tagElement = form.closest('.tag');
                tagElement.style.opacity = '0.6';

                try {
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        tagElement.remove();
                        this.showFlashMessage(data.message, 'success');
                        
                        // Show "no tags" message if no tags left
                        const tagsContainer = document.querySelector('.tags-container');
                        if (tagsContainer.children.length === 0) {
                            const noTagsSpan = document.createElement('span');
                            noTagsSpan.className = 'no-tags';
                            noTagsSpan.textContent = 'No tags added yet';
                            tagsContainer.appendChild(noTagsSpan);
                        }
                    } else {
                        tagElement.style.opacity = '1';
                        this.showFlashMessage(data.message || 'Error removing tag', 'error');
                    }
                } catch (error) {
                    console.error('Error removing tag:', error);
                    tagElement.style.opacity = '1';
                    this.showFlashMessage('Error removing tag: ' + error.message, 'error');
                }
            }

            showFlashMessage(message, type) {
                // Remove existing flash messages
                const existingFlash = document.querySelector('.flash-message');
                if (existingFlash) {
                    existingFlash.remove();
                }

                // Create new flash message
                const flashDiv = document.createElement('div');
                flashDiv.className = `flash-message flash-${type}`;
                flashDiv.textContent = message;

                document.body.appendChild(flashDiv);

                // Auto remove after 4 seconds
                setTimeout(() => {
                    if (flashDiv.parentNode) {
                        flashDiv.remove();
                    }
                }, 4000);
            }
        }

        // Initialize Product Quantity Manager (from your existing code)
        class ProductQuantityManager {
            constructor() {
                this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                this.initEventListeners();
            }

            initEventListeners() {
                document.addEventListener('click', (e) => {
                    if (e.target.matches('.btn-increase') || e.target.closest('.btn-increase')) {
                        e.preventDefault();
                        const button = e.target.matches('.btn-increase') ? e.target : e.target.closest('.btn-increase');
                        this.handleQuantityChange(button, 'increase');
                    }

                    if (e.target.matches('.btn-decrease') || e.target.closest('.btn-decrease')) {
                        e.preventDefault();
                        const button = e.target.matches('.btn-decrease') ? e.target : e.target.closest('.btn-decrease');
                        this.handleQuantityChange(button, 'decrease');
                    }
                });
            }

            async handleQuantityChange(button, action) {
                const productId = button.dataset.productId;
                const quantityElement = document.querySelector(`.quantity-${productId}`);
                const statusElement = document.querySelector(`.status-${productId}`);
                
                button.disabled = true;
                const originalText = button.innerHTML;
                button.innerHTML = '...';

                try {
                    const response = await fetch(`/products/${productId}/${action}-quantity`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        if (quantityElement) {
                            quantityElement.textContent = data.quantity;
                        }
                        
                        if (statusElement && data.status) {
                            statusElement.textContent = this.formatStatus(data.status);
                        }
                        
                        this.showFlashMessage(data.message, 'success');
                    } else {
                        this.showFlashMessage(data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.showFlashMessage('An error occurred. Please try again.', 'error');
                } finally {
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            }

            formatStatus(status) {
                return status.split('_').map(word => 
                    word.charAt(0).toUpperCase() + word.slice(1)
                ).join(' ');
            }

            showFlashMessage(message, type) {
                const existingFlash = document.querySelector('.flash-message');
                if (existingFlash) {
                    existingFlash.remove();
                }

                const flashDiv = document.createElement('div');
                flashDiv.className = `flash-message flash-${type}`;
                flashDiv.textContent = message;
                document.body.appendChild(flashDiv);

                setTimeout(() => {
                    if (flashDiv.parentNode) {
                        flashDiv.remove();
                    }
                }, 3000);
            }
        }

        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            new TagManager();
            new ProductQuantityManager();
            console.log('All managers initialized successfully');
        });
    </script>
</x-layout>