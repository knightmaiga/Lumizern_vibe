<?php
/* Template Name: Vibe Quiz 2025 - Enhanced */
if (!defined('ABSPATH')) exit;
get_header();

$central_vibe_data = apply_filters('lumizern_central_vibe_data', [
    'cozy-cocoon' => [
        'emoji' => '🛋️', 'name' => 'Cozy Cocoon', 'color' => '#FF6B6B',
        'description' => 'You thrive in warm, comfortable environments and value relaxation and self-care above all else.',
        'characteristics' => ['Comfort-focused', 'Nurturing', 'Home-oriented', 'Practical'],
        'shopUrl' => '/vibe/cozy-cocoon',
        'shopDescription' => 'Discover warm, comfortable products for your perfect cozy space',
        'benefits' => ['Better sleep quality', 'Reduced stress levels', 'Enhanced relaxation', 'Improved mental clarity'],
        'products' => ['Comfortable loungewear', 'Soft blankets', 'Scented candles', 'Cozy home decor', 'Relaxation tools']
    ],
    'power-play' => [
        'emoji' => '💼', 'name' => 'Power Play', 'color' => '#4ECDC4',
        'description' => 'You are ambitious, driven, and value efficiency and success in both personal and professional life.',
        'characteristics' => ['Ambitious', 'Organized', 'Goal-oriented', 'Strategic'],
        'shopUrl' => '/vibe/power-play',
        'shopDescription' => 'Tools and accessories for the ambitious professional',
        'benefits' => ['Increased productivity', 'Professional confidence', 'Time management', 'Goal achievement'],
        'products' => ['Professional accessories', 'Productivity tools', 'Office organization', 'Tech gadgets', 'Career development']
    ]
    // ... include all other vibes with same structure
]);
?>

<div class="quiz-container-enhanced">
  <section class="quiz-hero-enhanced">
    <div class="container-enhanced">
      <h1 class="quiz-title-enhanced">Discover Your <span class="text-gradient-enhanced">Energy Signature</span></h1>
      <p class="quiz-subtitle-enhanced">8 quick questions to match your vibe.</p>
      <div class="question-counter">Question <span class="current-question">1</span> of 8</div>
    </div>
  </section>

  <div class="container-enhanced quiz-content-enhanced">
    <form id="vibe-quiz-enhanced" class="quiz-form-enhanced">

      <?php
      $steps = [
        1 => ['When you wake up, your first thought is...', [
          ['🌅 Creating a peaceful routine', ['cozy-cocoon'=>3,'zen-chill'=>2]],
          ['🎯 Conquering your to-do list', ['power-play'=>3,'creative-hustle'=>1]],
          ['💡 A brilliant new idea', ['creative-hustle'=>3,'aesthetic-curator'=>2]],
          ['🐾 Caring for family/pets', ['pawfectionist'=>3,'cozy-cocoon'=>2]],
        ]],
        2 => ['Your ideal workspace looks like...', [
          ['🖼️ Minimal and design-led', ['aesthetic-curator'=>3,'creative-hustle'=>2]],
          ['🌿 Plants and calm light', ['zen-chill'=>3,'cozy-cocoon'=>2]],
          ['⚡ Multiple monitors, command center', ['power-play'=>3,'creative-hustle'=>1]],
          ['🛋️ Comfort nook', ['cozy-cocoon'=>3,'pawfectionist'=>2]],
        ]],
        3 => ['After a long week, you recharge by...', [
          ['🧘 Solo quiet time', ['zen-chill'=>3,'cozy-cocoon'=>2]],
          ['🎪 Social events', ['power-play'=>3,'creative-hustle'=>1]],
          ['🎨 Personal projects', ['creative-hustle'=>3,'aesthetic-curator'=>2]],
          ['❤️ Family/pet time', ['pawfectionist'=>3,'cozy-cocoon'=>2]],
        ]],
        4 => ['When purchasing, you prioritize...', [
          ['✨ Design & brand', ['aesthetic-curator'=>3,'power-play'=>1]],
          ['☁️ Comfort & function', ['zen-chill'=>3,'cozy-cocoon'=>2]],
          ['🎭 Uniqueness', ['creative-hustle'=>3,'pawfectionist'=>1]],
          ['🚀 Performance', ['power-play'=>3,'aesthetic-curator'=>1]],
        ]],
        5 => ['You learn best through...', [
          ['🔨 Hands-on doing', ['creative-hustle'=>3,'aesthetic-curator'=>1]],
          ['📊 Structured courses', ['power-play'=>3,'zen-chill'=>1]],
          ['📖 Reflection & reading', ['zen-chill'=>3,'cozy-cocoon'=>1]],
          ['👥 Collaboration', ['pawfectionist'=>3,'creative-hustle'=>1]],
        ]],
        6 => ['When stressed, you tend to...', [
          ['🏠 Retreat to comfort', ['cozy-cocoon'=>3,'zen-chill'=>2]],
          ['⚔️ Attack with a plan', ['power-play'=>3,'creative-hustle'=>1]],
          ['🎵 Create something', ['creative-hustle'=>3,'aesthetic-curator'=>2]],
          ['🤗 Seek support', ['pawfectionist'=>3,'zen-chill'=>1]],
        ]],
        7 => ['In 5 years, you see yourself...', [
          ['👑 Leading and scaling', ['power-play'=>3,'aesthetic-curator'=>1]],
          ['🌟 Creating legacy work', ['creative-hustle'=>3,'zen-chill'=>1]],
          ['⚖️ Living balanced', ['zen-chill'=>3,'cozy-cocoon'=>1]],
          ['🏡 In family/community flow', ['pawfectionist'=>3,'cozy-cocoon'=>1]],
        ]],
        8 => ['What matters most now?', [
          ['💎 Beauty & excellence', ['aesthetic-curator'=>3,'power-play'=>1]],
          ['🕊️ Peace & mindfulness', ['zen-chill'=>3,'cozy-cocoon'=>1]],
          ['🎭 Expression & connection', ['creative-hustle'=>3,'pawfectionist'=>1]],
          ['🚀 Impact & achievement', ['power-play'=>3,'creative-hustle'=>1]],
        ]],
      ];
      foreach ($steps as $index => $step): ?>
      <div class="quiz-step-enhanced <?php echo $index === 1 ? 'active' : ''; ?>" data-step="<?php echo esc_attr($index); ?>">
        <h2 class="question-title-enhanced"><?php echo esc_html($step[0]); ?></h2>
        <div class="options-grid-enhanced">
          <?php foreach ($step[1] as $option): ?>
            <label class="option-card-enhanced" data-vibes='<?php echo wp_json_encode($option[1]); ?>'>
              <input type="radio" name="q<?php echo esc_attr($index); ?>" value="<?php echo esc_attr(sanitize_title($option[0])); ?>" required>
              <div class="option-content-enhanced"><span class="option-text"><?php echo esc_html($option[0]); ?></span></div>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>

      <div class="quiz-navigation-enhanced">
        <label class="quiz-optin"><input type="checkbox" id="quiz-email-optin" value="1"> Save my vibe profile for future recommendations</label>
        <button type="button" class="nav-btn-enhanced prev-btn-enhanced" style="display:none">Previous</button>
        <button type="button" class="nav-btn-enhanced next-btn-enhanced">Next</button>
        <button type="submit" class="nav-btn-enhanced submit-btn-enhanced" style="display:none">Reveal My Vibe</button>
      </div>
    </form>

    <div id="quiz-loading-enhanced" class="quiz-loading-enhanced hidden"><p>Analyzing your vibe...</p></div>
    <div id="quiz-result-enhanced" class="quiz-result-enhanced hidden"></div>
  </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function () {
  class VibeQuizEnhanced {
    constructor() {
      this.currentStep = 1;
      this.totalSteps = 8;
      this.answers = {};
      this.selectedScoresByQuestion = {};
      this.vibeScores = {
        'cozy-cocoon': 0,
        'power-play': 0,
        'aesthetic-curator': 0,
        'zen-chill': 0,
        'creative-hustle': 0,
        'pawfectionist': 0
      };
      this.bindEvents();
      this.updateNavigation();
    }

    bindEvents() {
      document.querySelectorAll('.option-card-enhanced input').forEach((radio) => {
        radio.addEventListener('change', (event) => {
          const step = parseInt(event.target.name.replace('q',''), 10);
          const questionKey = 'q' + step;
          const card = event.target.closest('.option-card-enhanced');
          const vibes = JSON.parse(card.dataset.vibes || '{}');

          const previousScores = this.selectedScoresByQuestion[questionKey] || {};
          Object.keys(previousScores).forEach((v) => {
            this.vibeScores[v] = (this.vibeScores[v] || 0) - (parseInt(previousScores[v], 10) || 0);
          });

          this.answers[questionKey] = event.target.value;
          this.selectedScoresByQuestion[questionKey] = vibes;

          Object.keys(vibes).forEach((v) => {
            this.vibeScores[v] = (this.vibeScores[v] || 0) + (parseInt(vibes[v], 10) || 0);
          });

          this.updateNavigation();
        });
      });

      document.querySelector('.next-btn-enhanced').addEventListener('click', () => this.nextStep());
      document.querySelector('.prev-btn-enhanced').addEventListener('click', () => this.prevStep());
      document.querySelector('.submit-btn-enhanced').addEventListener('click', (e) => this.submitQuiz(e));
    }

    showStep(step) {
      document.querySelectorAll('.quiz-step-enhanced').forEach((el) => el.classList.remove('active'));
      const target = document.querySelector('.quiz-step-enhanced[data-step="' + step + '"]');
      if (target) target.classList.add('active');
      const current = document.querySelector('.current-question');
      if (current) current.textContent = String(step);
    }

    nextStep() {
      if (!this.answers['q' + this.currentStep]) return;
      if (this.currentStep < this.totalSteps) {
        this.currentStep += 1;
        this.showStep(this.currentStep);
        this.updateNavigation();
      }
    }

    prevStep() {
      if (this.currentStep > 1) {
        this.currentStep -= 1;
        this.showStep(this.currentStep);
        this.updateNavigation();
      }
    }

    updateNavigation() {
      const prev = document.querySelector('.prev-btn-enhanced');
      const next = document.querySelector('.next-btn-enhanced');
      const submit = document.querySelector('.submit-btn-enhanced');
      prev.style.display = this.currentStep > 1 ? 'inline-flex' : 'none';
      if (this.currentStep === this.totalSteps) {
        next.style.display = 'none';
        submit.style.display = this.answers['q8'] ? 'inline-flex' : 'none';
      } else {
        next.style.display = this.answers['q' + this.currentStep] ? 'inline-flex' : 'none';
        submit.style.display = 'none';
      }
    }

    async saveProfile(primary, secondary, tertiary) {
      if (!window.lumizernConfig || !lumizernConfig.ajax_url || !lumizernConfig.quiz_profile_nonce) return;
      const optIn = document.getElementById('quiz-email-optin');
      const body = new URLSearchParams({
        action: 'lumizern_save_quiz_profile',
        nonce: lumizernConfig.quiz_profile_nonce,
        primary,
        secondary,
        tertiary,
        email_opt_in: optIn && optIn.checked ? '1' : ''
      });
      try {
        await fetch(lumizernConfig.ajax_url, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
          body: body.toString()
        });
      } catch (error) {
        console.warn('Unable to persist vibe profile', error);
      }
    }

    submitQuiz(event) {
      event.preventDefault();
      if (Object.keys(this.answers).length < this.totalSteps) return;
      document.getElementById('quiz-loading-enhanced').classList.remove('hidden');
      document.getElementById('vibe-quiz-enhanced').style.display = 'none';

      setTimeout(() => {
        const sorted = Object.keys(this.vibeScores).sort((a,b)=>this.vibeScores[b]-this.vibeScores[a]);
        const primary = sorted[0];
        const secondary = sorted[1];
        const tertiary = sorted[2];
        const vibeData = <?php echo wp_json_encode($central_vibe_data); ?>;
        const fallback = {
          'aesthetic-curator': {name:'Aesthetic Curator',emoji:'✨',shopUrl:'/vibe/aesthetic-curator'},
          'zen-chill': {name:'Zen Chill',emoji:'🧘',shopUrl:'/vibe/zen-chill'},
          'creative-hustle': {name:'Creative Hustle',emoji:'🎨',shopUrl:'/vibe/creative-hustle'},
          'pawfectionist': {name:'Pawfectionist',emoji:'🐾',shopUrl:'/vibe/pawfectionist'}
        };
        const p = vibeData[primary] || fallback[primary];
        this.saveProfile(primary, secondary, tertiary);
        const s = vibeData[secondary] || fallback[secondary];
        const t = vibeData[tertiary] || fallback[tertiary];
        document.getElementById('quiz-loading-enhanced').classList.add('hidden');
        const result = document.getElementById('quiz-result-enhanced');
        result.classList.remove('hidden');
        result.innerHTML = `
          <h2>Your Primary Vibe: ${p.emoji} ${p.name}</h2>
          <p>${p.description || ''}</p>
          <p><strong>Secondary:</strong> ${s.emoji} ${s.name} · <strong>Tertiary:</strong> ${t.emoji} ${t.name}</p>
          <p><a class="nav-btn-enhanced submit-btn-enhanced" href="${p.shopUrl}">Shop Your Vibe</a></p>
          <p><button class="nav-btn-enhanced" onclick="window.location.reload()">Retake Quiz</button></p>
        `;
      }, 1500);
    }
  }
  new VibeQuizEnhanced();
});
</script>

<?php get_footer(); ?>
