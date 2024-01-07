/*
npm install sharp imagemin imagemin-pngquant imagemin-mozjpeg

*/

import fs from "fs";
import path from "path";
import sharp from "sharp";
import imagemin from "imagemin";
import imageminPngquant from "imagemin-pngquant";
import imageminMozjpeg from "imagemin-mozjpeg";

// Get the current module's directory
const currentModuleDir = "D:/wamp/www/tailormade/uploads/images";

const inputDirectory = "D:/wamp/www/tailormade/uploads/images/lp";
const outputDirectory = "D:/wamp/www/tailormade/uploads/images/lp/thumbnails";

// Ensure the output directory exists
if (!fs.existsSync(outputDirectory)) {
  fs.mkdirSync(outputDirectory);
}

// Read the list of files in the input directory
fs.readdirSync(inputDirectory).forEach((file) => {
  const inputPath = path.join(inputDirectory, file);
  const outputPath = path.join(outputDirectory, file);

  // Resize the image using sharp
  sharp(inputPath)
    .resize({ width: 300 }) // Set the desired width
    .toFile(outputPath, (err, info) => {
      if (err) {
        console.error(`Error resizing ${file}:`, err);
      } else {
        console.log(`Resized ${file} to ${info.width}x${info.height}`);
      }
    });
});

// Optimize images using imagemin
imagemin([`${outputDirectory}/*.{jpg,png}`], {
  destination: outputDirectory,
  plugins: [
    imageminMozjpeg({ quality: 80 }), // Adjust quality as needed
    imageminPngquant({ quality: [0.6, 0.8] }), // Adjust quality as needed
  ],
}).then(() => {
  console.log("Optimization complete");
});
