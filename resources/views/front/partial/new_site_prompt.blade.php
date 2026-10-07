<style>
    .new-site-prompt {
        position: fixed;
        z-index: 10000;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(0, 0, 0, 0.55);
    }

    .new-site-prompt-card {
        width: 100%;
        max-width: 440px;
        padding: 28px 24px 20px;
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.28);
    }

    .new-site-prompt-card p {
        margin: 0 0 20px;
        font-size: 1.35rem;
        line-height: 1.45;
        text-align: center;
    }

    .new-site-prompt-actions {
        display: flex;
        flex-direction: column;
    }

    .new-site-prompt-actions .btn {
        width: 100%;
        height: auto;
        margin: 0 0 10px;
        padding: 10px 16px;
        line-height: 1.4;
        text-transform: none;
        font-size: 1.05rem;
    }

    .new-site-prompt-yes {
        background-color: #107db7;
    }

    .new-site-prompt-yes:hover,
    .new-site-prompt-yes:focus {
        background-color: #0573a6;
    }

    .new-site-prompt-no {
        background-color: #fff;
        color: #107db7;
        border: 1px solid #107db7;
        box-shadow: none;
    }

    .new-site-prompt-no:hover,
    .new-site-prompt-no:focus {
        background-color: #f3f8fb;
    }

    .new-site-prompt-never {
        background-color: transparent;
        color: #757575;
        box-shadow: none;
    }

    .new-site-prompt-never:hover,
    .new-site-prompt-never:focus {
        background-color: #f5f5f5;
    }
</style>
<div id="new-site-prompt" class="new-site-prompt" role="dialog" aria-modal="true" aria-labelledby="new-site-prompt-title">
    <div class="new-site-prompt-card">
        <p id="new-site-prompt-title">Queres experimentar o novo site do Domingo às Dez?</p>
        <form method="POST" action="{{ route('front.new_site_prompt') }}">
            @csrf
            <input type="hidden" name="choice" value="">
            <div class="new-site-prompt-actions">
                <button type="submit" class="btn new-site-prompt-yes" onclick="this.form.choice.value='yes'">Sim</button>
                <button type="submit" class="btn new-site-prompt-no" onclick="this.form.choice.value='no'">Não</button>
                <button type="submit" class="btn new-site-prompt-never" onclick="this.form.choice.value='never'">Não voltar a perguntar</button>
            </div>
        </form>
    </div>
</div>
