-- Update frame paths from old numeric names to new meaningful names
-- Run this script to fix 404 errors for frame images

UPDATE `frames` SET `src` = 'public/images/frame-normal.png' WHERE `src` = 'public/images/6.png';
UPDATE `frames` SET `src` = 'public/images/frame-cat.png' WHERE `src` = 'public/images/20.png';
UPDATE `frames` SET `src` = 'public/images/frame-star.png' WHERE `src` = 'public/images/21.png';
UPDATE `frames` SET `src` = 'public/images/frame-crazy-1.png' WHERE `src` = 'public/images/22.png';
UPDATE `frames` SET `src` = 'public/images/frame-crazy-2.png' WHERE `src` = 'public/images/23.png';
UPDATE `frames` SET `src` = 'public/images/frame-friends.png' WHERE `src` = 'public/images/24.png';
UPDATE `frames` SET `src` = 'public/images/frame-mybeloved.png' WHERE `src` = 'public/images/25.png';
UPDATE `frames` SET `src` = 'public/images/frame-quanque.png' WHERE `src` = 'public/images/26.png';
UPDATE `frames` SET `src` = 'public/images/frame-longtimenosee.png' WHERE `src` = 'public/images/27.png';
UPDATE `frames` SET `src` = 'public/images/frame-vintage-1.png' WHERE `src` = 'public/images/7.png';
UPDATE `frames` SET `src` = 'public/images/frame-y2k.png' WHERE `src` = 'public/images/8.png';
UPDATE `frames` SET `src` = 'public/images/frame-vietnamese-vertical.png' WHERE `src` = 'public/images/11.png';
UPDATE `frames` SET `src` = 'public/images/frame-vietnamese-square.png' WHERE `src` = 'public/images/12.png';
UPDATE `frames` SET `src` = 'public/images/frame-crazy-square.png' WHERE `src` = 'public/images/13.png';
UPDATE `frames` SET `src` = 'public/images/frame-1989.png' WHERE `src` = 'public/images/14.png';
UPDATE `frames` SET `src` = 'public/images/frame-papers.png' WHERE `src` = 'public/images/10.png';

-- Also update sample_image column if it exists
UPDATE `frames` SET `sample_image` = 'public/images/frame-normal.png' WHERE `sample_image` = 'public/images/6.png';
UPDATE `frames` SET `sample_image` = 'public/images/frame-cat.png' WHERE `sample_image` = 'public/images/20.png';
UPDATE `frames` SET `sample_image` = 'public/images/frame-star.png' WHERE `sample_image` = 'public/images/21.png';
UPDATE `frames` SET `sample_image` = 'public/images/frame-crazy-1.png' WHERE `sample_image` = 'public/images/22.png';
UPDATE `frames` SET `sample_image` = 'public/images/frame-crazy-2.png' WHERE `sample_image` = 'public/images/23.png';
UPDATE `frames` SET `sample_image` = 'public/images/frame-friends.png' WHERE `sample_image` = 'public/images/24.png';
UPDATE `frames` SET `sample_image` = 'public/images/frame-mybeloved.png' WHERE `sample_image` = 'public/images/25.png';
UPDATE `frames` SET `sample_image` = 'public/images/frame-quanque.png' WHERE `sample_image` = 'public/images/26.png';
UPDATE `frames` SET `sample_image` = 'public/images/frame-longtimenosee.png' WHERE `sample_image` = 'public/images/27.png';
UPDATE `frames` SET `sample_image` = 'public/images/frame-vintage-1.png' WHERE `sample_image` = 'public/images/7.png';
UPDATE `frames` SET `sample_image` = 'public/images/frame-y2k.png' WHERE `sample_image` = 'public/images/8.png';
UPDATE `frames` SET `sample_image` = 'public/images/frame-vietnamese-vertical.png' WHERE `sample_image` = 'public/images/11.png';
UPDATE `frames` SET `sample_image` = 'public/images/frame-vietnamese-square.png' WHERE `sample_image` = 'public/images/12.png';
UPDATE `frames` SET `sample_image` = 'public/images/frame-crazy-square.png' WHERE `sample_image` = 'public/images/13.png';
UPDATE `frames` SET `sample_image` = 'public/images/frame-1989.png' WHERE `sample_image` = 'public/images/14.png';
UPDATE `frames` SET `sample_image` = 'public/images/frame-papers.png' WHERE `sample_image` = 'public/images/10.png';

