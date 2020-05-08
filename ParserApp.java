import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.PrintWriter;

import org.apache.tika.exception.TikaException;
import org.apache.tika.metadata.Metadata;
import org.apache.tika.parser.ParseContext;
import org.apache.tika.parser.Parser;
import org.apache.tika.parser.html.HtmlParser;
import org.apache.tika.sax.BodyContentHandler;
import org.xml.sax.SAXException;

public class ParserApp {

	public static void main(String[] args) throws IOException, TikaException, SAXException{
		// TODO Auto-generated method stub
		
		PrintWriter writer = new PrintWriter("C:\\Users\\shwet\\eclipse-workspace\\bigLatimes.txt");
		String path = "C:\\Users\\shwet\\Downloads\\LATIMES\\latimes\\latimes\\";	
		File allFilesDir = new File(path);
		int count = 0;
		for(File file: allFilesDir.listFiles()) {
			count+=1;
			
			HtmlParser parser = new HtmlParser();
		    BodyContentHandler handler = new BodyContentHandler(-1);
		    Metadata metadata = new Metadata();
		    FileInputStream inputstream = new FileInputStream(file);
		    ParseContext context = new ParseContext();

		    parser.parse(inputstream, handler, metadata, context);
		    
		    String sentences = handler.toString();
		    String wordsList[] = sentences.split(" ");
		    
		    for(String s: wordsList)				// Extract words of each file
			{
				if(s.matches("[a-zA-Z]+\\.?"))
				{
					writer.print(s + " ");
					
				}
			}
		    System.out.println(count);
		}
		
		writer.close();
		System.out.println("Parse Complete");
		
	}
}
