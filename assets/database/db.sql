
USE nutrition_system;

CREATE TABLE caregiver (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE children (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_username VARCHAR(255) NOT NULL,
    child_name VARCHAR(50) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    dob DATE NOT NULL,
    weight DECIMAL(5,2) NOT NULL,
    height DECIMAL(5,2) NOT NULL,
    dietary_restrictions TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE child_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    child_id INT,
    weight DECIMAL(5,2),
    height DECIMAL(5,2),
    schedule VARCHAR(50),
    dietary_restrictions TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE CASCADE
);

CREATE TABLE meal_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    age_group VARCHAR(50) NOT NULL,  
    meal_time ENUM('Breakfast', 'Mid-Morning Snack', 'Lunch', 'Afternoon Snack', 'Dinner') NOT NULL,
    meal_type ENUM('Affordable', 'Premium') NOT NULL,
    meal_name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    min_weight DECIMAL(5,2) NOT NULL,  
    max_weight DECIMAL(5,2) NOT NULL  
);




INSERT INTO meal_plans (age_group, meal_time, meal_type, meal_name, description, min_weight, max_weight)
VALUES 
('0-6 months', 'Breakfast', 'Affordable', 'Exclusive Breastfeeding', 'Breast milk only, no solids.', 0, 7),
('0-6 months', 'Breakfast', 'Premium', 'Fortified Infant Formula', 'Iron-fortified formula for infants.', 0, 7),

('0-6 months', 'Lunch', 'Affordable', 'Exclusive Breastfeeding', 'Breast milk continues to be the main food.', 0, 7),
('0-6 months', 'Lunch', 'Premium', 'Fortified Infant Formula', 'Fortified formula providing essential nutrients.', 0, 7);


INSERT INTO meal_plans (age_group, meal_time, meal_type, meal_name, description, min_weight, max_weight)
VALUES 
('6-12 months', 'Breakfast', 'Affordable', 'Porridge with Banana', 'Simple millet porridge with mashed banana.', 7, 10),
('6-12 months', 'Breakfast', 'Premium', 'Oats with Milk & Fruit', 'Oats with warm milk and banana slices.', 7, 10),

('6-12 months', 'Mid-Morning Snack', 'Affordable', 'Boiled Sweet Potatoes', 'Boiled sweet potatoes with a little butter.', 7, 10),
('6-12 months', 'Mid-Morning Snack', 'Premium', 'Yogurt with Fruit', 'Plain yogurt with fresh fruit slices.', 7, 10),

('6-12 months', 'Lunch', 'Affordable', 'Mashed Beans & Ugali', 'Soft ugali with mashed beans and spinach.', 7, 10),
('6-12 months', 'Lunch', 'Premium', 'Rice with Chicken Stew', 'Rice with tender chicken stew and carrots.', 7, 10),

('6-12 months', 'Afternoon Snack', 'Affordable', 'Banana', 'Mashed banana with peanut butter.', 7, 10),
('6-12 months', 'Afternoon Snack', 'Premium', 'Avocado Toast', 'Whole wheat toast with mashed avocado.', 7, 10),

('6-12 months', 'Dinner', 'Affordable', 'Mashed Potatoes & Greens', 'Mashed potatoes with spinach.', 7, 10),
('6-12 months', 'Dinner', 'Premium', 'Fish with Mashed Potatoes', 'Fish fillet with mashed potatoes and peas.', 7, 10);


INSERT INTO meal_plans (age_group, meal_time, meal_type, meal_name, description, min_weight, max_weight)
VALUES 
('1-2 years', 'Breakfast', 'Affordable', 'Tea with Wholemeal Chapati', 'Mild tea with wholemeal chapati.', 10, 15),
('1-2 years', 'Breakfast', 'Premium', 'Scrambled Eggs & Toast', 'Soft scrambled eggs with whole grain toast.', 10, 15),

('1-2 years', 'Mid-Morning Snack', 'Affordable', 'Boiled Maize & Beans (Githeri)', 'A nutritious maize and beans snack.', 10, 15),
('1-2 years', 'Mid-Morning Snack', 'Premium', 'Fruit Salad & Yogurt', 'Sliced mangoes, bananas, and yogurt.', 10, 15),

('1-2 years', 'Lunch', 'Affordable', 'Rice & Lentil Stew', 'Cooked rice with lentil stew.', 10, 15),
('1-2 years', 'Lunch', 'Premium', 'Grilled Chicken & Vegetables', 'Grilled chicken with mashed potatoes and carrots.', 10, 15),

('1-2 years', 'Afternoon Snack', 'Affordable', 'Roasted Groundnuts', 'A handful of roasted groundnuts.', 10, 15),
('1-2 years', 'Afternoon Snack', 'Premium', 'Cheese Crackers', 'Cheese slices with whole wheat crackers.', 10, 15),

('1-2 years', 'Dinner', 'Affordable', 'Ugali & Sukuma Wiki', 'Ugali with mashed greens.', 10, 15),
('1-2 years', 'Dinner', 'Premium', 'Baked Fish & Mashed Potatoes', 'Baked fish fillet with mashed potatoes.', 10, 15);


INSERT INTO meal_plans (age_group, meal_time, meal_type, meal_name, description, min_weight, max_weight)
VALUES 
('2-3 years', 'Breakfast', 'Affordable', 'Uji (Porridge) & Banana', 'Traditional porridge with sliced banana.', 12, 18),
('2-3 years', 'Breakfast', 'Premium', 'Pancakes & Honey', 'Whole grain pancakes with honey.', 12, 18),

('2-3 years', 'Mid-Morning Snack', 'Affordable', 'Mandazi & Black Tea', 'Homemade mandazi with tea.', 12, 18),
('2-3 years', 'Mid-Morning Snack', 'Premium', 'Cheese & Whole-Grain Crackers', 'Healthy cheese slices with crackers.', 12, 18),

('2-3 years', 'Lunch', 'Affordable', 'Githeri', 'Cooked maize and beans with greens.', 12, 18),
('2-3 years', 'Lunch', 'Premium', 'Fried Rice with Chicken', 'Fried rice with pieces of grilled chicken.', 12, 18),

('2-3 years', 'Afternoon Snack', 'Affordable', 'Peanuts & Dried Fruits', 'Roasted peanuts with dried mango.', 12, 18),
('2-3 years', 'Afternoon Snack', 'Premium', 'Fruit Smoothie', 'Blended fruit smoothie with yogurt.', 12, 18),

('2-3 years', 'Dinner', 'Affordable', 'Ugali & Fried Cabbage', 'Soft ugali with stir-fried cabbage.', 12, 18),
('2-3 years', 'Dinner', 'Premium', 'Pasta & Meatballs', 'Whole wheat pasta with meatballs.', 12, 18);

INSERT INTO meal_plans (age_group, meal_time, meal_type, meal_name, description, min_weight, max_weight)
VALUES 
('3-4 years', 'Breakfast', 'Affordable', 'Porridge & Sweet Potato', 'Millet porridge with slices of sweet potato.', 13, 22),
('3-4 years', 'Breakfast', 'Premium', 'French Toast & Fruit', 'French toast with sliced fruits and honey.', 13, 22),

('3-4 years', 'Mid-Morning Snack', 'Affordable', 'Boiled Eggs & Banana', 'Boiled eggs with a ripe banana.', 13, 22),
('3-4 years', 'Mid-Morning Snack', 'Premium', 'Greek Yogurt & Berries', 'Plain yogurt with fresh berries.', 13, 22),

('3-4 years', 'Lunch', 'Affordable', 'Ugali with Sukuma & Eggs', 'Ugali with fried sukuma wiki and boiled egg.', 13, 22),
('3-4 years', 'Lunch', 'Premium', 'Beef Pilau & Veggies', 'Spiced rice with beef and mixed vegetables.', 13, 22),

('3-4 years', 'Afternoon Snack', 'Affordable', 'Carrot Sticks & Hummus', 'Raw carrot sticks with simple hummus.', 13, 22),
('3-4 years', 'Afternoon Snack', 'Premium', 'Fruit Kabobs', 'Fruit skewers with yogurt dip.', 13, 22),

('3-4 years', 'Dinner', 'Affordable', 'Rice with Cabbage Stew', 'Plain rice with cabbage and tomato sauce.', 13, 22),
('3-4 years', 'Dinner', 'Premium', 'Chicken Pasta & Broccoli', 'Whole grain pasta with grilled chicken and broccoli.', 13, 22);

INSERT INTO meal_plans (age_group, meal_time, meal_type, meal_name, description, min_weight, max_weight)
VALUES 
('4-5 years', 'Breakfast', 'Affordable', 'Tea with Wholemeal Bread', 'Mild tea with wholemeal bread.', 14, 25),
('4-5 years', 'Breakfast', 'Premium', 'Omelet & Toast', 'Omelet with vegetables and toast.', 14, 25),

('4-5 years', 'Mid-Morning Snack', 'Affordable', 'Boiled Cassava', 'Boiled cassava slices.', 14, 25),
('4-5 years', 'Mid-Morning Snack', 'Premium', 'Milk & Cookies', 'Milk with homemade cookies.', 14, 25),

('4-5 years', 'Lunch', 'Affordable', 'Beans with Chapati', 'Stewed beans with whole-wheat chapati.', 14, 25),
('4-5 years', 'Lunch', 'Premium', 'Rice with Fish Stew', 'Steamed rice with grilled fish stew.', 14, 25),

('4-5 years', 'Afternoon Snack', 'Affordable', 'Popcorn', 'Plain popcorn with minimal salt.', 14, 25),
('4-5 years', 'Afternoon Snack', 'Premium', 'Granola Bar', 'Homemade granola bar with honey.', 14, 25),

('4-5 years', 'Dinner', 'Affordable', 'Rice & Stewed Green Grams', 'Rice with green grams (Ndengu).', 14, 25),
('4-5 years', 'Dinner', 'Premium', 'Beef Stew & Brown Rice', 'Beef stew with brown rice and vegetables.', 14, 25);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    phone_number VARCHAR(20),
    amount INT,
    transaction_id VARCHAR(100),
    payment_date DATETIME,
    expiry_date DATETIME,
    status ENUM('Pending', 'Success', 'Failed') DEFAULT 'Pending',
    payment_amount INT NULL,
    status_message TEXT NULL
);
