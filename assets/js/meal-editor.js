document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-edit-meal]').forEach((button) => {
        button.addEventListener('click', () => {
            const mealItem = button.closest('.meal-item');
            const viewSection = mealItem.querySelector('.meal-view');
            const formSection = mealItem.querySelector('.meal-edit-form');
            viewSection.style.display = 'none';
            formSection.style.display = 'grid';
        });
    });

    document.querySelectorAll('.meal-edit-form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const mealItem = form.closest('.meal-item');
            const mealIndex = mealItem.getAttribute('data-meal-index');
            const formData = new FormData(form);

            fetch('update-plan.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    meal_index: mealIndex,
                    food: formData.get('food') || '',
                    quantity: formData.get('quantity') || '',
                    time: formData.get('time') || ''
                })
            })
                .then((response) => response.json())
                .then((result) => {
                    if (!result.success) {
                        throw new Error(result.message || 'Unable to update meal.');
                    }

                    const updatedMeal = result.plan.meals[mealIndex];
                    mealItem.querySelector('[data-field="food"]').textContent = updatedMeal.food;
                    mealItem.querySelector('[data-field="quantity"]').textContent = updatedMeal.quantity;
                    mealItem.querySelector('[data-field="time"]').textContent = updatedMeal.time;
                    mealItem.querySelector('[data-field="calories"]').textContent = updatedMeal.calories;
                    mealItem.querySelector('[data-field="protein"]').textContent = updatedMeal.protein;
                    mealItem.querySelector('[data-field="carbs"]').textContent = updatedMeal.carbs;
                    mealItem.querySelector('[data-field="fat"]').textContent = updatedMeal.fat;

                    document.querySelector('[data-summary="calories"]').textContent = result.plan.totals.calories;
                    document.querySelector('[data-summary="protein"]').textContent = result.plan.totals.protein + 'g';
                    document.querySelector('[data-summary="carbs"]').textContent = result.plan.totals.carbs + 'g';
                    document.querySelector('[data-summary="fat"]').textContent = result.plan.totals.fat + 'g';

                    mealItem.querySelector('.meal-view').style.display = 'block';
                    form.style.display = 'none';
                })
                .catch(() => {
                    mealItem.querySelector('.meal-view').style.display = 'block';
                    form.style.display = 'none';
                });
        });
    });

    document.querySelectorAll('.cancel-btn').forEach((button) => {
        button.addEventListener('click', () => {
            const mealItem = button.closest('.meal-item');
            mealItem.querySelector('.meal-view').style.display = 'block';
            mealItem.querySelector('.meal-edit-form').style.display = 'none';
        });
    });
});
