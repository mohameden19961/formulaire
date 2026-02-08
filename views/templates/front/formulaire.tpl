<div class="contact-form" id="contact-section">
    <!-- En-tête du formulaire -->
    <div class="form-header">
        {if $banner_image}
            <div class="form-banner">
                <img src="{$banner_image}" alt="Bannière" class="banner-image">
            </div>
        {/if}
        <h2 class="form-title">{$form_title}</h2>
    </div>

    <!-- Formulaire -->
    <form action="{$urls.current_url}#contact-section" method="post" class="contact-form-main">
        <!-- Champ Nom -->
        <div class="form-group">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" class="form-control" required>
        </div>
        
        <!-- Champ Email -->
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <!-- Champ Message -->
        <div class="form-group">
            <label for="message">Message:</label>
            <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
        </div>
        
        <!-- Bouton de soumission -->
        <button type="submit" name="submit" class="submit-btn">Envoyer</button>
    </form>

    <!-- Message de statut -->
    {if $message}
        <div class="message-status">
            {$message nofilter}
        </div>
    {/if}
</div>

