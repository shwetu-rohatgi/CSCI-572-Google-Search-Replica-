import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.OutputStreamWriter;
import java.io.Writer;
import java.util.HashMap;
import java.util.Map;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

public class ExtractLinks {

	public static void main(String[] args) throws Exception{
		// TODO Auto-generated method stub
		BufferedReader br = new BufferedReader(new FileReader("C:\\Users\\shwet\\Downloads\\LATIMES\\URLtoHTML_latimes_news.csv"));
		HashMap<String,String> filename_URL = new HashMap<String, String>();
		HashMap<String,String> URL_filename = new HashMap<String, String>();
		String line = br.readLine(); 	//first line will be headers
		line = br.readLine();
		
		while(line!=null) {
			String str[] = line.split(",");
			filename_URL.put(str[0],str[1]);
			URL_filename.put(str[1],str[0]);
			line = br.readLine();
		}
		
		//int count = 0;
		//Set<String> edges = new HashSet<String>();
		
		StringBuffer buffer = new StringBuffer();
		
		for(Map.Entry<String, String> entry: filename_URL.entrySet()) {
			//if(count>=10) break;
			//count+=1;
			
			StringBuffer newBuffer = new StringBuffer();
			
			String htmlName = entry.getKey(); 
			String url = entry.getValue();
			
			String baseDirectory = "C:\\Users\\shwet\\Downloads\\LATIMES\\latimes\\latimes\\";
			File file = new File("C:\\Users\\shwet\\Downloads\\LATIMES\\latimes\\latimes\\"+htmlName);
			Document doc = Jsoup.parse(file,"UTF-8",url);
			Elements links = doc.select("a[href]");
			
			print("\n"+htmlName+" Links: (%d) ",links.size());
			for (Element link: links) {
				//print("* a: <%s>",link.attr("abs:href"));
				String trimedLink = link.attr("abs:href");
				if (URL_filename.containsKey(trimedLink)) {
					if(newBuffer.length()>0) {
						newBuffer.append("\n");
					}
					newBuffer.append(baseDirectory + htmlName +" "+ baseDirectory + URL_filename.get(trimedLink));
				}
			}
			buffer.append(newBuffer);
			buffer.append("\n");
		}
		
		String path = "C:\\Users\\shwet\\eclipse-workspace\\networkEdgeList.txt";
		Writer writer = new BufferedWriter(new OutputStreamWriter(new FileOutputStream(path),"utf-8"));
		writer.write(buffer.toString());		
		writer.flush();
		writer.close();
	}
	
	private static void print(String msg, Object... args) {
		System.out.println(String.format(msg, args));
	}
}
